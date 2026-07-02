<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\JWTService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Register new user
     * @group Authentication
     * @bodyParam username string required Username. Example: newuser
     * @bodyParam email string required Email. Example: user@example.com
     * @bodyParam password string required Password (min 6 characters). Example: password123
     * @response 201 {
     *   "success": true,
     *   "message": "User registered successfully",
     *   "data": {
     *     "id_user": 1,
     *     "username": "newuser",
     *     "email": "user@example.com"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Validation failed",
     *   "errors": {
     *     "username": ["The username has already been taken."],
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|unique:tbl_user,username|max:50',
                'password' => 'required|string|min:6',
                'email' => 'required|string|email|unique:tbl_user,email|max:100',
            ]);

            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'email' => $validated['email'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'id_user' => $user->id_user,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user and return JWT token
     * @group Authentication
     * @bodyParam username string required Username. Example: admin
     * @bodyParam password string required Password. Example: password123
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful",
     *   "data": {
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *     "token_type": "Bearer",
     *     "expires_in": 86400,
     *     "refresh_expires_in": 604800,
     *     "user": {
     *       "id_user": 1,
     *       "username": "admin",
     *       "email": "admin@example.com"
     *     }
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid credentials"
     * }
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $user = User::where('username', $validated['username'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }

            $tokenData = $this->jwtService->generateToken($user);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'token' => $tokenData['token'],
                    'token_type' => $tokenData['token_type'],
                    'expires_in' => $tokenData['expires_in'],
                    'refresh_expires_in' => $tokenData['refresh_expires_in'],
                    'user' => [
                        'id_user' => $user->id_user,
                        'username' => $user->username,
                        'email' => $user->email,
                    ],
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout user (invalidate token)
     * @group Authentication
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful"
     * }
     */
    public function logout(Request $request)
    {
        try {
            // In a real application, you might want to blacklist the token
            // For now, we'll just return success
            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh JWT token
     * @group Authentication
     * @response 200 {
     *   "success": true,
     *   "message": "Token refreshed successfully",
     *   "data": {
     *     "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *     "token_type": "Bearer",
     *     "expires_in": 86400,
     *     "refresh_expires_in": 604800
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid or expired token"
     * }
     */
    public function refresh(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided',
                ], 401);
            }

            $newTokenData = $this->jwtService->refreshToken($token);

            if (!$newTokenData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => $newTokenData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user
     * @group Authentication
     * @response 200 {
     *   "success": true,
     *   "message": "User retrieved successfully",
     *   "data": {
     *     "id_user": 1,
     *     "username": "admin",
     *     "email": "admin@gmail.com"
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function me(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $userData = $this->jwtService->getUserFromToken($token);

            if (!$userData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token',
                ], 401);
            }

            $user = User::find($userData['user_id']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'id_user' => $user->id_user,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
