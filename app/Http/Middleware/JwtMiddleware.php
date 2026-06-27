<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use Closure;
use Illuminate\Http\Request;

class JwtMiddleware
{
    protected $jwtService;

    public function __construct(JWTService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not provided',
            ], 401);
        }

        $userData = $this->jwtService->getUserFromToken($token);

        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token',
            ], 401);
        }

        // Add user data to request for use in controllers
        $request->attributes->add(['user_id' => $userData['user_id']]);

        return $next($request);
    }
}
