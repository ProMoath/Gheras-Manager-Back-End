<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Project;
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
    public function index()
    {
        $this->authorize('viewAny', Project::class);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', Project::class);
        return response()->json([
            'success' => true,
            'data' => $project->load(['Tasks']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', Project::class);
        $validatedData = $request->validate([
            'name' => 'string',
            'description' => 'string|nullable|min::3|max:5000',
            'active' =>'boolean',
        ]);
        $project->update($validatedData);
        return response()->json([
            'success' => true,
            'data' => $project->load(['Tasks', 'Tasks.parentTask','Tasks.subTask']),
            'message' => "Project updated successfully."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', Project::class);
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => "Project deleted successfully."
        ]);

    }
}
