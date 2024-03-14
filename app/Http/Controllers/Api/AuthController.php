<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

/**
 * @OA\Info(title="API", version="0.1")
 */
class AuthController extends Controller
{
    use HttpResponses;
    /**
     * Register a new user.
     *
     * @OA\Post(
     *     path="/auth/register",
     *     tags={"auth"},
     *     operationId="register",
     *     summary="Register a new user",
     *     description="Create a new user with username, password, and password confirmation.",
     *     @OA\RequestBody(
     *         description="User registration data",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="User's name",
     *                    required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="User's mail",
     *                    required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     description="User's phone",
     *                    required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     description="User's username",
     *                    required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="User's password",
     *                    required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
     *                     description="Password confirmation",
     *                    required={"true"},
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *                @OA\Property(
     *                    property="user",
     *                    type="object",
     *                    @OA\Property(property="name", type="string", example="My Name Is Owner"),
     *                    @OA\Property(property="email", type="string", example="name@owner.mail"),
     *                    @OA\Property(property="username", type="string", example="ownername"),
     *                    @OA\Property(property="phone", type="string", example="082229111332"),
     *                    @OA\Property(property="id", type="string", example="UNk9K4qv"),
     *                    @OA\Property(property="updated_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *                    @OA\Property(property="created_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *                ),
     *         ),
     *     ),
     * )
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
     * 
     * 
     * @OA\Post(
     *    path="/auth/login",
     *    tags={"auth"},
     *    summary="Login a user",
     *    description="Login a user with username or email and password.",
     *    operationId="login",
     *     @OA\RequestBody(
     *         description="Login User",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="username",
     *                     type="string",
     *                     description="User's username",
     *                     required={"true"},
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                     description="User's password",
     *                     required={"true"},
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(
     *                property="data",
     *                type="object",
     *                @OA\Property(property="access_token", type="string", example="1|n2hiu136RYfhinoQkYySowrmhcCHesHtgJwOzmiP8ab44f87"),
     *                @OA\Property(property="token_type", type="string", example="Bearer"),
     *                @OA\Property(
     *                    property="user",
     *                    type="object",
     *                    @OA\Property(property="name", type="string", example="My Name Is Owner"),
     *                    @OA\Property(property="email", type="string", example="name@owner.mail"),
     *                    @OA\Property(property="username", type="string", example="ownername"),
     *                    @OA\Property(property="phone", type="string", example="082229111332"),
     *                    @OA\Property(property="id", type="string", example="UNk9K4qv"),
     *                    @OA\Property(property="updated_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *                    @OA\Property(property="created_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *               ),
     *             ),
     *         ),
     *     ),
     * )
     *      
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

        $token = $user->createToken('token')->plainTextToken;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 'User logged in successfully');
    }

    /**
     * Logout a user.
     * 
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"auth"},
     *     summary="Logout a user",
     *     description="Logout a user with token.",
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *        response=200,
     *        description="User logged out successfully",
     *        @OA\JsonContent(
     *            @OA\Property(property="status", type="boolean", example=true),
     *            @OA\Property(property="message", type="string", example="User logged out successfully"),
     *       ),
     *    ),
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return $this->successResponse([], 'User logged out successfully');
    }

    /**
     * Check token validity.
     * 
     * @OA\Get(
     *     path="/auth",
     *     tags={"auth"},
     *     summary="Check token validity",
     *     description="Check token validity.",
     *     operationId="checkToken",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token is valid",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token is valid"),
     *             @OA\Property(
     *                property="data",
     *                type="object",
     *                @OA\Property(property="access_token", type="string", example="1|n2hiu136RYfhinoQkYySowrmhcCHesHtgJwOzmiP8ab44f87"),
     *                @OA\Property(property="token_type", type="string", example="Bearer"),
     *                @OA\Property(
     *                    property="user",
     *                    type="object",
     *                    @OA\Property(property="name", type="string", example="My Name Is Owner"),
     *                    @OA\Property(property="email", type="string", example="name@owner.mail"),
     *                    @OA\Property(property="username", type="string", example="ownername"),
     *                    @OA\Property(property="phone", type="string", example="082229111332"),
     *                    @OA\Property(property="id", type="string", example="UNk9K4qv"),
     *                    @OA\Property(property="updated_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *                    @OA\Property(property="created_at", type="string", example="2024-01-26T00:04:43.000000Z"),
     *                ),
     *             ),
     *         ),
     *    ),
     * )
     * 
     */
    public function checkToken(Request $request)
    {
        return $this->successResponse([
            'user' => auth()->user(),
        ], 'Token is valid');
    }
}
