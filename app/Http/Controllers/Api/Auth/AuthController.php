<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Throwable;

class AuthController extends Controller
{
    /**
     * @throws Exception|Throwable
     */
    public function register(RegisterRequest $request)
    {
        try {
            $result = DB::transaction(function () use ($request) {
                $validatedData = $request->validated();
                $validatedData['password'] = Hash::make($validatedData['password']);
                $validatedData['role_id'] = $validatedData['role_id'] ?? Role::volunteer;
                $validatedData['status'] = $validatedData['status'] ?? true;

                $user = User::create($validatedData);

                if (isset($validatedData['teams']))
                    $user->teams()->attach($validatedData['teams']);

                $token = $user->createToken('auth_token')->plainTextToken;
                return compact('user', 'token');
            });
            $result['user']->load(['role', 'teams']);

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $result['user'],
                    'token' => $result['token'],
                    'role_name' => $result['user']->role->name ?? Role::volunteer,
                ],
                'message' => 'user signup successfully',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while signing up',
                'error' => $e->getMessage(),
            ],500);
        }



    }

    public function login(LoginRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
                ],
            ], 401);
        }
        if (!$user->status) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'USER_INACTIVE',
                    'message' => 'حساب المستخدم غير مفعل !',
                ],
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $user->load('role','teams');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user->load('role'),
                'token' => $token,
                'role' => $user->role->name ?? Role::volunteer,
                'expires_in' => 86400,
            ],
            'message' => 'Login successful',
        ]);
    }
    public function logout(Request $request)
    {
        /** @var PersonalAccessToken $token */
           $token = $request->user()->currentAccessToken();
           $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'expires_in' => 86400,
            ],
        ]);
    }
}
