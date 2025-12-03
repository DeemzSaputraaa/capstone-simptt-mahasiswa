<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Laravel 10+ uses $middlewareAliases; keep backward compatibility
    protected $middlewareAliases = [
        'auth.session' => \App\Http\Middleware\CheckAuthSession::class,
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        'jwt.payload' => \App\Http\Middleware\JwtPayloadMiddleware::class,
    ];

    // For older Laravel versions that still read $routeMiddleware
    protected $routeMiddleware = [
        'auth.session' => \App\Http\Middleware\CheckAuthSession::class,
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        'jwt.payload' => \App\Http\Middleware\JwtPayloadMiddleware::class,
    ];
}
