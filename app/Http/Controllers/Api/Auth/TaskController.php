<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\TaskRequest;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $query = Task::with(['team','creator','assignee','project','sourceTask','linkedTask','parentTask']);

        // Filtering
        $filters= ['status','priority','team_id','project_id','assignee_id','created_by'];
        foreach ($filters as $filter) {
            if ($request->filled($filter))
                $query->where($filter,$request->input($filter));
        }

        // Date range filtering
        if ($request->filled('due_date_from'))
            $query->where('due_date', '>=', $request->due_date_from);

        if ($request->filled('due_date_to'))
            $query->where('due_date', '<=', $request->due_date_to);

        //Search
        if ($request->filled('search')){
            $search = $request->search;
            $query->where(function ($q) use ($search){
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        // Sorting
        $sortBy = $request->get('sort_by','created_at');
        $sortOrder = $request->get('sortOrder','desc');
        $query->orderBy($sortBy,$sortOrder);

        // Pagination
        $limit = min($request->get('limit', 20),100);
        $tasks = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $tasks->items(),
            'pagination' => [
                'page' => $tasks->currentPage(),
                'limit' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_pages' => $tasks->lastPage(),
            ]
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $validatedData = $request->validated();

        // Verify assignee belongs to team if provided
        // 2. التحقق من أن الشخص المسند إليه المهمة ينتمي لنفس الفريق (Logic Check)
        if(isset($validatedData['assignee_id']) && isset($validatedData['team_id'])){
            $team = Team::find($validatedData['team_id']);
            $assigneeInTeam = $team->users()->where('users.id',$validatedData['assignee_id'])->exists();
            if(!$assigneeInTeam){
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'code'=> 'INVALID_ASSIGNEE',
                        'message'=> 'Assignee must belong to the task team'
                    ]
                ],400);
            }
        }
        $validatedData['created_by'] = auth()->id();
        $validatedData['priority'] = $validatedData['priority']??'minor';
        $validatedData['parent_task_id']=$validatedData['parent_task_id']??null;
        $validatedData['work_hours']=$validatedData['work_hours']??0;
        $task = Task::create($validatedData);

         $task->linkedTask()->sync($request->input('linked_tasks'));
         $data=$task->load(['team','creator','assignee','project','sourceTask','linkedTask','parentTask']);
        return response()->json([
            'success' => true,
            'data' => $data,
            'message'=> 'Task created successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $data=$task->load(['team','creator','assignee','project','sourceTask','linkedTask','parentTask','subTask']);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validatedData = $request->validated();
        /*$validatedData = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:3|max:5000',
            'priority' => 'nullable|in:critical,major,minor',
            'statue' => 'nullable|in:open,in_progress,testing,resolved',
            'type' => 'nullable|in:new,bug',
            'due_date' => 'nullable|date|after:today',
            'assignee_id' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'team_id' => 'nullable|exists:teams,id',
            'parent_task_id' => 'nullable|exists:tasks,id',
            'work_hours' => 'nullable|numeric|min:0|max:168',

        ]);*/
        // Validate status transition logic
        if (isset($validatedData['status']) && $validatedData['statue'] !== $task->status) {
            if (!$task->canTransitionTo($validatedData['statue'])) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'code' => 'INVALID_STATUS_TRANSITION',
                        'message' => "Cannot transition from {$task->status} to {$validatedData['status']}",
                    ]
                ], 400);
            }
        }
        $task->update($validatedData);
        if ($request->has('linked_tasks')) $task->linkedTask()->sync($request->input('linked_tasks'));
        return response()->json([
            'success' => true,
            'data' => $task->load(['team','creator','assignee','project']),
            'message'=> 'Task updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'success' => true,
            'message'=> 'Task deleted successfully',
        ]);
    }
    public function userTasks(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        $task=$task->assignee();
        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function teamTasks(Team $team)
    {
        $this->authorize('viewAny', $team);
        $tasks=$team->tasks();
        return response()->json([
            'success' => true,
            'data' =>$tasks,
        ]);
    }
    public function linkTasks(Task $sourceTask, array $linkedTaskIds)
    {
        $sourceProjectId = $sourceTask->project_id;

        // إذا كانت المهمة الأصلية لا تنتمي لمشروع، لا يمكن ربطها
        if (!$sourceProjectId) {
            throw ValidationException::withMessages([
                'project' => ['لا يمكن ربط مهام غير تابعة لمشروع.'],
            ]);
        }

        // 2. التحقق من أن جميع المهام المراد ربطها تابعة لنفس المشروع
        $invalidTasks = Task::whereIn('id', $linkedTaskIds) ->where('project_id', '!=', $sourceProjectId) ->pluck('id');

        if ($invalidTasks->isNotEmpty()) {
            throw ValidationException::withMessages([
                'link_tasks' => ['لا يمكن ربط المهمة برقم (' . $invalidTasks->implode(', ') . ') لأنها لا تنتمي لنفس المشروع.'],
            ]);
        }

        // 3. تنفيذ عملية الربط (إذا تم تجاوز كل التحقق)
        // syncWithoutDetaching تستخدم لإضافة علاقات دون حذف العلاقات القديمة
        $sourceTask->linkedTasks()->syncWithoutDetaching($linkedTaskIds);

        return $sourceTask->load('linkedTasks');
    }

    public function updateStatus(Task $task)
    {

    }

}
