<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Todo\StoreTodoRequest;
use App\Http\Requests\Todo\UpdateTodoRequest;
use App\Interfaces\TodoServiceInterface;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TodoController
 *
 * Depends on TodoServiceInterface — not the concrete TodoService.
 * Uses HasApiResponse trait — HTTP status codes resolved automatically.
 *
 * @package App\Http\Controllers\Api
 */
class TodoController extends Controller
{
    use HasApiResponse;

    public function __construct(private readonly TodoServiceInterface $todoService) {}

    /**
     * GET /api/todos?search=keyword&page=1
     */
    public function index(Request $request): JsonResponse
    {
        return $this->respond(
            $this->todoService->index(auth()->id(), $request->query('search'))
        );
    }

    /**
     * POST /api/todos
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        return $this->respond(
            $this->todoService->store(auth()->id(), $request->validated()),
            201
        );
    }

    /**
     * GET /api/todos/{id}
     */
    public function show(int $id): JsonResponse
    {
        return $this->respond(
            $this->todoService->show(auth()->id(), $id)
        );
    }

    /**
     * PUT /api/todos/{id}
     */
    public function update(UpdateTodoRequest $request, int $id): JsonResponse
    {
        return $this->respond(
            $this->todoService->update(auth()->id(), $id, $request->validated())
        );
    }

    /**
     * DELETE /api/todos/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->respond(
            $this->todoService->destroy(auth()->id(), $id)
        );
    }
}
