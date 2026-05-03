<?php

namespace App\Interfaces;

use App\libs\Response\GlobalApiResponse;

/**
 * Interface AuthServiceInterface
 *
 * Defines the contract for authentication operations.
 * Controllers depend on this interface, not the concrete service —
 * making it easy to swap implementations or mock in tests.
 *
 * @package App\Interfaces
 */
interface AuthServiceInterface
{
    public function register(array $data): GlobalApiResponse;

    public function verifyEmail(string $code): GlobalApiResponse;

    public function login(array $credentials): GlobalApiResponse;

    public function logout(): GlobalApiResponse;
}
