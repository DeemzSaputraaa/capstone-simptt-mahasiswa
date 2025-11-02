<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;


class CheckAuthSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userData = Session::get('user_data');
        $token = Session::get('auth_token');

        // Check if session exists
        if (!$userData || !$token) {
            return response()->json([
                'success' => false,
                'code' => 'UNAUTHORIZED',
                'message' => 'User not authenticated'
            ], 401);
        }

        // Check if session expired
        if ($userData['expires_at'] < now()->timestamp) {
            Session::flush();
            return response()->json([
                'success' => false,
                'code' => 'SESSION_EXPIRED',
                'message' => 'Session has expired'
            ], 401);
        }

        // Attach user data to request
        $request->merge(['auth_user' => $userData]);

        return $next($request);
    }
}