<?php

namespace App\Traits;

use App\libs\Response\GlobalApiResponse;
use App\libs\Response\GlobalApiResponseCodeBook;
use Illuminate\Support\Facades\Log;

/**
 * Trait ApiResponseTrait
 *
 * Reusable response builder helpers shared across all services.
 * Instead of repeating `(new GlobalApiResponse())->error(...)` everywhere,
 * services just call `$this->success(...)` or `$this->error(...)`.
 *
 * @package App\Traits
 */
trait ApiResponseTrait
{
    /**
     * Build a success response.
     */
    protected function success(
        string $message,
        mixed  $records = [],
        int    $total   = 0
    ): GlobalApiResponse {
        $count = $total > 0 ? $total : (is_array($records) && !empty($records) ? 1 : 0);

        return (new GlobalApiResponse())->success($message, $count, $records);
    }

    /**
     * Build a generic error response.
     */
    protected function error(
        array  $code,
        string $message,
        array  $errors = []
    ): GlobalApiResponse {
        return (new GlobalApiResponse())->error($code, $message, $errors);
    }

    /**
     * Record not found — centralized so the message is always consistent.
     */
    protected function notFound(string $message = 'Record not found.'): GlobalApiResponse
    {
        return $this->error(GlobalApiResponseCodeBook::RECORD_NOT_EXIST, $message);
    }

    /**
     * Something blew up — log it and return a safe message to the client.
     */
    protected function serverError(string $context, \Throwable $e, string $message = 'Something went wrong. Please try again later.'): GlobalApiResponse
    {
        Log::error("{$context}: {$e->getMessage()}", [
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
        ]);

        return $this->error(GlobalApiResponseCodeBook::INTERNAL_SERVER_ERROR, $message);
    }

    /**
     * Already exists — e.g. duplicate email on register.
     */
    protected function alreadyExists(string $message): GlobalApiResponse
    {
        return $this->error(GlobalApiResponseCodeBook::RECORD_ALREADY_EXIST, $message);
    }

    /**
     * Invalid credentials response.
     */
    protected function invalidCredentials(string $message): GlobalApiResponse
    {
        return $this->error(GlobalApiResponseCodeBook::INVALID_CREDENTIALS, $message);
    }

    /**
     * Email not verified response.
     */
    protected function emailNotVerified(string $message): GlobalApiResponse
    {
        return $this->error(GlobalApiResponseCodeBook::EMAIL_NOT_VERIFIED, $message);
    }
}
