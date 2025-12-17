<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Models\ApiUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    use ApiResponse;
    /**
     * Register a new API user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:api_users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $apiUser = ApiUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status' => 'active',
        ]);

        $token = $apiUser->createToken('api-token')->plainTextToken;
        return $this->success([
            'user' => $apiUser,
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * Login API user and return token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $apiUser = ApiUser::where('email', $request->email)->first();

        if (! $apiUser || ! Hash::check($request->password, $apiUser->password)) {
            return $this->error('Email or password is incorrect.', 403);
        }

        if ($apiUser->status !== 'active') {
            return $this->error('Your account is inactive. Please contact administrator.', 403);
        }
        // Revoke old tokens (optional - remove if you want multiple active tokens)
        $apiUser->tokens()->delete();
        $token = $apiUser->createToken('api-token', ['*'])->plainTextToken;

        return $this->success([
            'user' => $apiUser,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success(['user' => $request->user('api_users')]);
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user('api_users')->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Revoke all tokens for the user
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user('api_users')->tokens()->delete();

        return $this->success(null, 'All tokens revoked successfully');
    }
}
