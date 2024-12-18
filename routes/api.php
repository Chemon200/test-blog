<?php

use App\Http\Middleware\ApiAdminAuthMiddleware;
use App\User\Infraestructure\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/api/user/register', [RegisterController::class, 'register'])->middleware(ApiAdminAuthMiddleware::class);;

