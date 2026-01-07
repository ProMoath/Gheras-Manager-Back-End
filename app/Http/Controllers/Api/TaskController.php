<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Http\Request;

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
        $query = Task::with(['team','creator','assignee','project']);

        // Filtering
        $filters= ['status','priority','team_id','project_id','assignee_id','created_by'];
        foreach ($filters as $filter) {
            $query->where($filter,$request->filter);
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
                'total_pages' => $tasks->lastPage(),
            ]
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();

        // Verify assignee belongs to team if provided
        if($validatedData['assignee_id']??null){
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
        $validatedData['work_hours']=$validatedData['work_hours']??0;
        $task = Task::create($validatedData);

        if ($request->has('linked_tasks')) {
            $task->linkedTask()->sync($request->linked_tasks);
        }
        return response()->json([
            'success' => true,
            'data' => $task->load(['creator','editor','assignee','team','linkedTask','parentTask','project','']),
            'message'=> 'Task created successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return response()->json([
            'success' => true,
            'data' => $task->load(['team','creator','assignee','project']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Task $task)
    {
        $this->authorize('update', $task);

     $validatedData = $request->validate([
         'title' => 'required|string|min:3|max:255',
         'description' => 'required|string|min:3|max:5000',
         'priority' => 'nullable|in:critical,major,minor',
         'statue'=> 'nullable|in:open,in_progress,testing,resolved',
         'type'=> 'nullable|in:new,bug',
         'due_date' => 'nullable|date|after:today',
         'assignee_id' => 'nullable|exists:users,id',
         'project_id' => 'nullable|exists:projects,id',
         'team_id' => 'nullable|exists:teams,id',
         'parent_task_id'=> 'nullable|exists:tasks,id',
         'work_hours' => 'nullable|numeric|min:0|max:168',

     ]);
        // Validate status transition
        if(isset($validatedData['statue']) && $validatedData['statue']!==$task->status){
            if (!$task->canTransitionTo($validatedData['statue'])){
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'code'=> 'INVALID_STATUS_TRANSITION',
                        'message'=> "Cannot transition from {$task->status} to {$validatedData['status']}",
                    ]
                ],400);
            }
        }
        $task->update($validatedData);
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

    public function userTasks(Request $request, $userId)
    {
        //does user exist?
        $user = \App\Models\User::findOrFail($userId);

        // 2. هل لك صلاحية رؤية مهامه؟ (اختياري حسب الـ Policy)
         $this->authorize('viewAny', Task::class);

       //fetch tasks
        $tasks = $user->assignedTasks()
        ->with(['project', 'team']) // Eager loading
        ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

}
