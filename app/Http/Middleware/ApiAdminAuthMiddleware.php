<?php

declare( strict_types = 1 );

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helper\JwtAuth;
use App\Helper\ResponseApi;

class ApiAdminAuthMiddleware
{
    protected ResponseApi $responseApi;
    protected JwtAuth $jwtAuth;

    public function __construct()
    {
        $this->responseApi = new ResponseApi();
        $this->jwtAuth = new JwtAuth();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization', null);

        if (empty($token)) {
            return $this->responseApi->jsonError(401, ['errors' => 'El usuario no se ha identificado correctamente']);
        }
        
        $tokenData = $this->jwtAuth->checkAdminToken($token, true);

        if ($tokenData) {
            return $next($request, $tokenData);
        }

        return $this->responseApi->jsonError(401, ['errors' => 'El usuario no se ha identificado correctamente']);
    }
}