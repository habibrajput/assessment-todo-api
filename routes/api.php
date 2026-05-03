<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TodoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api (configured in bootstrap/app.php or
| RouteServiceProvider depending on your Laravel version).
|
| Auth routes are open (no JWT required).
| Todo routes are protected by our custom jwt middleware.
|
*/

// ── Authentication ────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {

    // POST   /api/auth/register    – Create an account
    Route::post('register', [AuthController::class, 'register']);

    // GET    /api/auth/verify/{code} – Click-through link from the e-mail
    Route::get('verify/{code}', [AuthController::class, 'verifyEmail']);

    // POST   /api/auth/login       – Receive a JWT
    Route::post('login', [AuthController::class, 'login']);

    // POST   /api/auth/logout      – Invalidate the current JWT
    Route::post('logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
});

// ── Todos (authenticated only) ────────────────────────────────────────────────
Route::middleware('jwt.auth')->group(function () {

    // GET    /api/todos            – Paginated list (optional ?search=keyword)
    // POST   /api/todos            – Create a new todo
    // GET    /api/todos/{id}       – View a single todo
    // PUT    /api/todos/{id}       – Replace a todo's title + description
    // DELETE /api/todos/{id}       – Remove a todo
    Route::apiResource('todos', TodoController::class);
});
