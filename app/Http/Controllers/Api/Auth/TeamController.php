<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\TeamRequest;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
     * @throws \Throwable
     */
    public function store(TeamRequest $request)
    {
        $this->authorize('create', Team::class);
        $validatedData=$request->validated();
        $slug = Str::slug($validatedData['name']);
        if (Team::where('slug', $slug)->exists())
            $slug = $slug . '-' . time();
        $team = Team::create([
            'name' => $validatedData['name'],
            'slug' => $slug,
            'members_count' => 0,
            ]);
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
        $data=$team->load(['users','tasks' => function ($query)
    {
        $query->limit(5);
    }
    ]);
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $this->authorize('update', $team);
        $validatedData = $request->validated();
        if (isset($validatedData['name'])) {
            $validatedData['slug'] = Str::slug($validatedData['name']);
        }
        $team->update($validatedData);
        return response()->json([
            'success' => true,
            'data' => $team->load('users')->fresh(),
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
        $this->authorize('view', $team);
        $members=$team->users()->withPivot('role')->paginate(20);
        return response()->json([
            'success' => true,
            'data' => $members->items(),
            'pagination' => [
                'total' => $members->total(),
                'per_page' => $members->perPage(),
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
            ],
            'message' => "Team members retrieved successfully."
        ]);
    }
}
