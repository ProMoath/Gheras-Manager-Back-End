<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\TaskRequest;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use function Laravel\Prompts\table;

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
        $user = request()->user();
        $query = Task::with(['team','creator','assignees','project','sourceTask','linkedTask','parentTask']);

        if ($user->isVolunteer()) {
            $query->where(function ($q) use ($user) {
                $q->whereHas('assignees', function ($subQ) use ($user) {
                    $subQ->where('users.id', $user->id);
                })
                    ->orWhere('created_by', $user->id);
            });
        }

        // Filtering
        if ($request->filled('assignees_id')) {
            $assigneeId = (array) $request->input('assignees_id');
            $query->whereHas('assignees', function ($q) use ($assigneeId) {
                $q->whereIn('users.id', $assigneeId);
            });
        }

        $filters= ['status','priority','team_id','project_id','created_by'];
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
        if (!in_array($sortBy, ['created_at', 'due_date', 'priority', 'status'])) // Protection from SQL Injections
            $sortBy = 'created_at';
        $sortOrder = $request->get('sortOrder','desc') === 'desc' ? 'desc' : 'asc';
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
     * @throws \Throwable
     */
    public function store(TaskRequest $request)
    {
        $validatedData = $request->validated();
        $user = $request->user();

        $validatedData['created_by'] = auth()->id();
        $validatedData['priority'] = $validatedData['priority'] ?? 'minor';
        $validatedData['parent_task_id'] = $validatedData['parent_task_id'] ?? null;
        $validatedData['work_hours'] = $validatedData['work_hours'] ?? 0;

        try {
            return DB::transaction(function () use ($request, $validatedData, $user) {

                $task = Task::create($validatedData);

                if ($user->isVolunteer()) // Security Logic: Volunteer restriction
                    $task->assignees()->attach($request->user()->id);
                elseif ($request->has('assignees_id')) // or isset($validatedData['assignees_id']) | $request->filled('assignees_id' what is better?
                    $task->assignees()->sync($request->input('assignees_id'));

                if ($request->has('linked_tasks'))
                    $this->validateAndLinkTasks($task, $request->input('linked_tasks'));
                $data = $task->load(['team', 'creator', 'assignees', 'project', 'sourceTask', 'linkedTask', 'parentTask']);
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'message' => 'Task created successfully'
                ], 201);
            });
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Task creation failed',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getMessage() : null // مفيد لك أثناء التطوير
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $data=$task->load(['team','creator','assignees','project','sourceTask','linkedTask','parentTask','subTask']);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validatedData = $request->validated();
        // Validate status transition logic
        try {


            return DB::transaction(function () use ($request, $task, $validatedData) {
                if (isset($validatedData['status']) && $validatedData['status'] !== $task->status) {
                    if (!$task->canTransitionTo($validatedData['status'])) {
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
                if ($request->has('assignees_id'))
                {
                $newAssignees = $request->input('assignees_id');
                $task->assignees()->sync($newAssignees);
                }
                if ($request->has('linked_tasks'))
                    $this->validateAndLinkTasks($task, $request->input('linked_tasks'));

                return response()->json([
                    'success' => true,
                    'data' => $task->load(['team', 'creator', 'assignees', 'project']),
                    'message' => 'Task updated successfully',
                ]);
            });
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Task update failed',
                'error' => $e->getMessage(),
                'debug' => config('app.debug') ? $e->getMessage() : null
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->assignees()->detach();
        $task->delete();

        return response()->json([
            'success' => true,
            'message'=> 'Task deleted successfully',
        ]);
    }
    private function validateAndLinkTasks(Task $sourceTask, array $linkedTaskIds)
    {
        $sourceProjectId=$sourceTask->project_id;
        if(!$sourceProjectId)
            return;
        $invalidTasks=Task::whereIn('id',$linkedTaskIds)
            ->where('project_id','!=',$sourceProjectId)->pluck('id');
        if($invalidTasks->isNotEmpty())
        {
            throw ValidationException::withMessages([
            'linked_tasks' => ['لا يمكن ربط المهام (' . $invalidTasks->implode(', ') . ') لأنها لا تنتمي لنفس المشروع.']]);
        }
        $sourceTask->linkedTasks()->sync($linkedTaskIds);
    }
    public function userTasks(Request $request,User $user)
    {
        if ($request->user()->id !== $user->id && !$request->user()->isAdmin())
            abort(403, 'Unauthorized');

        $task =$user->assignedTasks()->with(['project','team'])->paginate(20);
        return response()->json([
            'success' => true,
            'data' => $task
        ]);
    }

    public function teamTasks(Team $team)
    {
        $this->authorize('view', $team);
        $tasks=$team->tasks()->paginate(20);
        return response()->json([
            'success' => true,
            'data' =>$tasks,
        ]);
    }


    public function updateStatus(Request $request,Task $task)
    {
        $this->authorize('update', $task);
        $request->validate([
            'status' => 'required|in:open,in_progress,testing,resolved'
        ]);
        $newStatus=$request->input('status');
        if($task->status !== $newStatus && !$task->canTransitionTo($newStatus))
            return response()->json([
                'success' => false,
                'errors' => [
                    'code' => 'INVALID_STATUS_TRANSITION',
                    'message' => "Cannot transition from {$task->status} to {$newStatus}",
                ]
            ],422);
        $task->update(['status'=>$newStatus]);
        return response()->json([
            'success' => true,
            'data' => $task->fresh(),
            'message'=> 'Task updated successfully',
        ]);
    }

    public function assignToUser(Request $request,Task $task)
    {
        $this->authorize('update', $task);
        $request->validate(['user_id' => 'required|integer|exists:users,id']);
        $userId=$request->input('user_id');
        $task->assignees()->syncWithoutDetaching([$userId]);

        $data=$task->load(['assignees','project','team']);
        return response()->json([
            'success' => true,
            'data' =>$data,
            'message'=> 'Task assigned successfully',
        ]);
    }
    public function removeFromUser(Request $request,Task $task)
    {
        $this->authorize('update', $task);
        $request->validate(['user_id' => 'required|integer|exists:users,id']);
        $userId=$request->input('user_id');
        $task->assignees()->detach([$userId]);

        $data=$task->load(['assignees','project','team']);
        return response()->json([
            'success' => true,
            'data' =>$data,
            'message'=> 'User removed successfully',
        ]);
    }

}
