<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\UserProfileResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
        $this->authorize('viewAny', User::class);
        $query = User::query()->with('role');// Eager loading

        // Filtering
        if($request->filled('role_id'))
            $query->where('users.role_id',$request->role_id);
        if($request->filled('status'))
            $query->where('users.status',$request->boolean('status'));
        if($request->filled('search'))
        {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like',"%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        // sorting
        $sortBy = $request->get('sort_by','created_at');
        if (!in_array($sortBy, ['created_at', 'name', 'email'])) { // حماية من الحقول العشوائية
            $sortBy = 'created_at';
        }
        $sortOrder = $request->get('sort_order','desc');
        $query->orderBy($sortBy,$sortOrder);


        //pagination
        $limit = min($request->get('limit',20),100);
        $users = $query->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'pagination' => [
                'page' => $users->currentPage(),
                'limit' => $users->perPage(),
                'total_count' => $users->total(),
                'total_pages' => $users->lastPage(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegisterRequest $request)
    {
        $this->authorize('create', User::class);

        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['role_id']=$validatedData['role_id'] ?? Role::volunteer;

        $user = User::create($validatedData);

        if(isset($validatedData['teams']))
            $user->teams()->attach($validatedData['teams']);
        return response()->json([
            'success' => true,
            'data' => $user->load('teams'),
            'message' => "User created successfully."
        ],201);

    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load('teams','role');
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $validatedData = $request->validated();
        if($request->filled('password'))
            $validatedData['password'] = Hash::make($validatedData['password']);
        else
            unset($validatedData['password']);// إزالة الحقل حتى لا يتم تحديثه بقيمة فارغة

        $user->update($validatedData);

       if(isset($validatedData['teams']))
            $user->teams()->sync($validatedData['teams']);

        return response()->json([
            'success' => true,
            'data' => $user->load('teams','role'),
            'message' => "User updated successfully."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            $this->authorize('delete', $user);
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => "User deleted successfully."
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() // Message in the model will appear here
            ], 400);
        }
    }
    public function assignTeam(Request $request,User $user)
    {
        $this->authorize('update',$user);
        $validatedData = $request->validate([
            'team_id' => 'required|integer|exists:teams,id',
        ]);
        $user->teams()->syncWithoutDetaching([$validatedData['team_id']]);

        return response()->json([
            'success' => true,
            'message' => "User assigned to team successfully."
        ]);
    }
    public function removeTeam(Request $request,User $user)
    {
        $this->authorize('delete',$user);

        $validatedData = $request->validate([
            'team_id' => 'required|integer|exists:teams,id',
        ]);
        $user->teams()->detach([$validatedData['team_id']]);

        return response()->json([
            'success' => true,
            'message' => "User removed from team successfully."
        ]);
    }

    public function toggleStatus(User $user)
    {
        $this->authorize('update',$user);

        $user->status =!$user->status;
        $user->save();

        $statusText = $user->status ? 'تفعيل' : 'تعطيل';
        return response()->json([
            'success' => true,
            'message' => "It has been {$statusText} successfully. ",
            'data' => ['status' => $user->status]
        ]);
    }
    public function getProfile(Request $request,User $user)
    {
        $this->authorize('view',$user);
        $user =auth()->user();
        $user->load(['teams','role']);
        return response()->json([
            'success' => true,
            'data' => new UserProfileResource($user),
            'message' => "User {$user->name} profile retrieved successfully."
        ],200);
    }
}
