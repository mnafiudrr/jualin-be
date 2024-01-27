<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HttpResponses;

    /**
     * Create a new user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'username' => 'required|unique:users|string|min:6',
            'email' => 'required|unique:users|string|email',
            'phone' => 'required|unique:users|string|min:10',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => strtolower($request->username),
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'User created successfully', 201);
    }

    /**
     * Login a user.
     * can be done with username or email
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required_without:email|string',
            'email' => 'required_without:username|string|email',
            'password' => 'required|string|min:8',
        ]);

        if (!auth()->attempt($request->only('username', 'password')))
            if (!auth()->attempt($request->only('email', 'password')))
                return $this->errorResponse('Invalid login credentials', 'Unauthorized', 401);
        
        $user = User::where('username', $request->username)->orWhere('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'User logged in successfully');
    }

    /**
     * Logout a user.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], 'User logged out successfully');
    }

    /**
     * Check token validity.
     */
    public function checkToken(Request $request)
    {
        return $this->successResponse([
            'user' => auth()->user(),
        ], 'Token is valid');
    }
}
