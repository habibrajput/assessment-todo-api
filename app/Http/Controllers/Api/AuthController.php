<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\AuthServiceInterface;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 *
 * Depends on AuthServiceInterface — not the concrete AuthService.
 * Uses HasApiResponse trait — no HTTP status codes are hardcoded here.
 * Each method is 2 lines: call service, return response.
 *
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use HasApiResponse;

    public function __construct(private readonly AuthServiceInterface $authService) {}

    /**
     * POST /api/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->respond($this->authService->register($request->validated()), 201);
    }

    /**
     * GET /api/auth/verify/{code}
     */
    public function verifyEmail(string $code): JsonResponse
    {
        return $this->respond($this->authService->verifyEmail($code));
    }

    /**
     * POST /api/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->respond($this->authService->login($request->validated()));
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(): JsonResponse
    {
        return $this->respond($this->authService->logout());
    }
}
