<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\apiAuthMiddleware;
use App\User\Infraestructure\RegisterController;

// RUTAS DE TEST
Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba/animals/{name?}', [App\Http\Controllers\pruebaController::class, 'index']);

Route::get('/test_orm', [App\Http\Controllers\pruebaController::class, 'testOrm']);


//RUTAS DE LA APLICACIÓN
#Route::post('/api/user/register', [RegisterController::class, 'register']);
Route::post('/api/user/login', [App\Http\Controllers\UserController::class, 'login']);
Route::put('/api/user/update', [UserController::class, 'update'])->middleware(apiAuthMiddleware::class);
Route::post('/api/user/upload', [UserController::class, 'upload'])->middleware(apiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename?}', [UserController::class, 'getImage']);
Route::get('/api/user/profile/{userId?}', [UserController::class, 'profile']);

Route::resource('/api/category', CategoryController::class);

Route::resource('/api/post', PostController::class);
Route::post('/api/post/upload', [PostController::class, 'upload']);
Route::get('/api/post/image/{filename?}', [PostController::class, 'getImage']);
Route::get('/api/post/category/{id}', [PostController::class, 'getPostsByCategory']);
Route::get('/api/post/user/{id}', [PostController::class, 'getPostsByUser']);
