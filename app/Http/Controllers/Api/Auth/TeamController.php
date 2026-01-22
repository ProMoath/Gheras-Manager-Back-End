<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
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
        $this->authorize('viewAny', Team::class);

        $query = Team::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $query->orderBy('created_at', 'desc');

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        $teams = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $teams->items(),
            'pagination' => [
                'total' => $teams->total(),
                'per_page' => $teams->perPage(),
                'current_page' => $teams->currentPage(),
                'last_page' => $teams->lastPage(),
            ],
            'message' => 'Teams retrieved successfully'
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Team::class);
        $data=$this->validate($request, [
            'name' => 'required|string|min:2',
            'slug' => 'required|string|min:2|unique:teams,slug',
            'members_count' => 'required|integer'
        ]);
        $team=Team::create($request->$data);
        return response()->json([
            'success' => true,
            'data' => $team,
            'message' => 'Team created successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $this->authorize('view', $team);
        $data=$team->load('tasks','users');
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $this->authorize('update', $team);
        $validatedData = $request->validate([
            'name' => 'string|min:2|max:255',
            'slug' => 'string|min:2|max:255| unique:teams,slug,'.$team->id,
        ]);
        $team->update($validatedData);
        return response()->json([
            'success' => true,
            'data' => $team->load(['tasks','users']),
            'message' => "Team updated successfully."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        try {
            $this->authorize('delete', Team::class);
            $team->delete();
            return response()->json([
                'success' => true,
                'message' => "Team deleted successfully."
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ],400);
        }
    }

    public function members(Team $team)
    {
        $this->authorize('viewAny', $team);
        $members=$team->users();
        return response()->json([
            'success' => true,
            'data' => $team->load('users'),$members
        ]);
    }
}
