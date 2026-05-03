<?php

namespace App\Services;

use App\Interfaces\TodoServiceInterface;
use App\libs\Response\GlobalApiResponse;
use App\Models\Todo;
use App\Traits\ApiResponseTrait;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class TodoService
 *
 * Each public method orchestrates small, focused private helpers.
 * Uses model scopes so queries read like plain English.
 *
 * @package App\Services
 */
class TodoService implements TodoServiceInterface
{
    use ApiResponseTrait;

    /**
     * Return a paginated, optionally filtered list of todos.
     */
    public function index(int $userId, ?string $search = null, int $perPage = 10): GlobalApiResponse
    {
        try {
            $paginated = $this->getPaginatedTodos($userId, $search, $perPage);

            return $this->success(
                'To-do list retrieved successfully.',
                $this->buildListPayload($paginated),
                $paginated->total()
            );
        } catch (\Throwable $e) {
            return $this->serverError('TodoService@index', $e, 'Could not retrieve the to-do list.');
        }
    }

    /**
     * Create and persist a new todo item.
     */
    public function store(int $userId, array $data): GlobalApiResponse
    {
        try {
            $todo = $this->createTodo($userId, $data);

            return $this->success(
                'To-do item created successfully.',
                ['todo' => $todo->toPublicArray()]
            );
        } catch (\Throwable $e) {
            return $this->serverError('TodoService@store', $e, 'Could not create the to-do item.');
        }
    }

    /**
     * Return a single todo scoped to the authenticated user.
     */
    public function show(int $userId, int $todoId): GlobalApiResponse
    {
        try {
            $todo = $this->findForUser($userId, $todoId);

            if (!$todo) {
                return $this->notFound('To-do item not found or you do not have permission to view it.');
            }

            return $this->success(
                'To-do item retrieved successfully.',
                ['todo' => $todo->toPublicArray()]
            );
        } catch (\Throwable $e) {
            return $this->serverError('TodoService@show', $e, 'Could not retrieve the to-do item.');
        }
    }

    /**
     * Update an existing todo item.
     */
    public function update(int $userId, int $todoId, array $data): GlobalApiResponse
    {
        try {
            $todo = $this->findForUser($userId, $todoId);

            if (!$todo) {
                return $this->notFound('To-do item not found or you do not have permission to update it.');
            }

            $this->applyUpdates($todo, $data);

            return $this->success(
                'To-do item updated successfully.',
                ['todo' => $todo->fresh()->toPublicArray()]
            );
        } catch (\Throwable $e) {
            return $this->serverError('TodoService@update', $e, 'Could not update the to-do item.');
        }
    }

    /**
     * Permanently delete a todo item.
     */
    public function destroy(int $userId, int $todoId): GlobalApiResponse
    {
        try {
            $todo = $this->findForUser($userId, $todoId);

            if (!$todo) {
                return $this->notFound('To-do item not found or you do not have permission to delete it.');
            }

            $todo->delete();

            return $this->success('To-do item deleted successfully.');
        } catch (\Throwable $e) {
            return $this->serverError('TodoService@destroy', $e, 'Could not delete the to-do item.');
        }
    }

    /**
     * Run the paginated query using model scopes.
     * Scopes make this read like a plain English sentence.
     */
    private function getPaginatedTodos(int $userId, ?string $search, int $perPage): LengthAwarePaginator
    {
        return Todo::forUser($userId)
            ->search($search)
            ->latestFirst()
            ->paginate($perPage);
    }

    /**
     * Build the records payload for the list response.
     * Keeps index() clean — it doesn't need to know about pagination shape.
     */
    private function buildListPayload(LengthAwarePaginator $paginated): array
    {
        return [
            'todos'      => $paginated->items(),
            'pagination' => $this->buildPaginationMeta($paginated),
        ];
    }

    /**
     * Extract pagination metadata into a clean, consistent array.
     */
    private function buildPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page'  => $paginator->currentPage(),
            'last_page'     => $paginator->lastPage(),
            'per_page'      => $paginator->perPage(),
            'total'         => $paginator->total(),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
        ];
    }

    /**
     * Persist a new todo row in the database.
     */
    private function createTodo(int $userId, array $data): Todo
    {
        return Todo::create([
            'user_id'     => $userId,
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Apply the incoming field changes to the model and save.
     */
    private function applyUpdates(Todo $todo, array $data): void
    {
        $todo->update([
            'title'       => $data['title'],
            'description' => $data['description'],
        ]);
    }

    /**
     * Find a todo that belongs to a specific user.
     * Returns null if not found — callers decide the response.
     */
    private function findForUser(int $userId, int $todoId): ?Todo
    {
        return Todo::forUser($userId)->find($todoId);
    }
}
