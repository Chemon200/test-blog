<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helper\JwtAuth;

class apiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        
        $jwtAuth = new JwtAuth();
        $tokenData = $jwtAuth->checkToken($token, true);

        if ($tokenData) {
            return $next($request, $tokenData);
        }

        return response()->json(
            [
            'code' => 400,
            'status' => 'error',
            'message' => 'El usuario no se ha identificado correctamente'
            ], 400
        );
    }
}
