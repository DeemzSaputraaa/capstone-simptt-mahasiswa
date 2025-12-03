<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtPayloadMiddleware
{
    /**
     * Parse token without loading user from database.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload()->toArray();

            // Optional: simple expiry check using custom claim
            if (isset($payload['expires_at']) && $payload['expires_at'] < now()->timestamp) {
                return response()->json([
                    'success' => false,
                    'code' => 'SESSION_EXPIRED',
                    'message' => 'Session has expired. Please login again.',
                ], 401);
            }

            // Tempelkan payload ke request untuk dipakai controller
            $request->attributes->set('jwt_payload', $payload);

            return $next($request);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => 'User not authenticated. Please login first.',
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'code' => 'INTERNAL_SERVER_ERROR',
                'message' => 'An error occurred during authentication.',
            ], 500);
        }
    }
}
