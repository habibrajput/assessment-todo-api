<?php

namespace App\Interfaces;

use App\libs\Response\GlobalApiResponse;

/**
 * Interface TodoServiceInterface
 *
 * Defines the contract for all Todo CRUD operations.
 *
 * @package App\Interfaces
 */
interface TodoServiceInterface
{
    public function index(int $userId, ?string $search = null, int $perPage = 10): GlobalApiResponse;

    public function store(int $userId, array $data): GlobalApiResponse;

    public function show(int $userId, int $todoId): GlobalApiResponse;

    public function update(int $userId, int $todoId, array $data): GlobalApiResponse;

    public function destroy(int $userId, int $todoId): GlobalApiResponse;
}
