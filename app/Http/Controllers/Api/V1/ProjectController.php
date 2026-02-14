<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProjectRequest;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Project::class);
        $query = Project::with(['tasks','creator','editor']);

        $filters=['status','name','active'];
        foreach ($filters as $filter) {
            if ($request->filled($filter))
                $query->where($filter, $request->input($filter));
        }

        if($request->has('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('name','like',"%{$search}%")
                    ->orWhere('description','like',"%{$search}%");
            });
        }
        $allowedSorts = ['created_at', 'name', 'active', 'status'];
        $sortBy = $request->get('sort_by','created_at');
        if(!in_array($sortBy, $allowedSorts))
            $sortBy = 'created_at';
        $sortOrder = $request->get('sortOrder','desc') === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sortBy,$sortOrder);

        $limit =min($request->get('limit',10),100);
        $projects = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $projects->items(),
            'pagination' => [
                'total' => $projects->total(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $this->authorize('create', Project::class);
        $validatedData=$request->validated();
        $validatedData['created_by']= auth()->id();
        $validatedData['status'] = $validatedData['status'] ?? 'open';
        $project=Project::create($validatedData);
        $data=$project->load(['tasks','creator']);
        return response()->json([
            'success'=>true,
            'data'=>$data,
            'message'=>'Project created successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', Project::class);
        $data=$project->load(['tasks','creator']);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @throws \Throwable
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorize('update', Project::class);
        $validatedData = $request->validated();
        try {
            return DB::transaction(function () use ($request, $project, $validatedData) {
                if (isset($validatedData['status']) && $validatedData['status'] !== $project->status && !$project->canTransitionTo($validatedData['status'])) {
                    return response()->json([
                        'success' => false,
                        'errors' => [
                            'code' => 'INVALID_STATUS_TRANSITION',
                            'message' => "Cannot transition from {$project->status} to {$validatedData['status']}",
                        ]
                    ], 422);
                }
                $project->update($validatedData);
                return response()->json([
                    'success' => true,
                    'data' => $project->load(['tasks', 'tasks.parentTask', 'tasks.subTask','creator'])->fresh(),
                    'message' => "Project updated successfully."
                ], 201);
            });
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Project update failed',
                'errors' => $exception->getMessage(),
                'debug' => config('app.debug') ? $exception->getMessage() : null
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => "Project deleted successfully."
        ]);

    }
}
