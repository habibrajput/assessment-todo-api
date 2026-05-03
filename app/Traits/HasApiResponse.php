<?php

namespace App\Traits;

use App\libs\Response\GlobalApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * Trait HasApiResponse
 *
 * Used in controllers to convert a GlobalApiResponse into an
 * HTTP JsonResponse with the correct status code.
 *
 * Keeps controllers even thinner — no status code logic lives there.
 *
 * @package App\Traits
 */
trait HasApiResponse
{
    /**
     * Convert a GlobalApiResponse to a JsonResponse.
     * Automatically picks the right HTTP status code based on outcome.
     */
    protected function respond(GlobalApiResponse $response, int $successStatus = 200): JsonResponse
    {
        $status = $response->isSuccess()
            ? $successStatus
            : $this->resolveErrorStatus($response->getOutcomeCode());

        return response()->json($response, $status);
    }

    /**
     * Map outcome codes to HTTP status codes.
     */
    private function resolveErrorStatus(int $outcomeCode): int
    {
        return match ($outcomeCode) {
            2       => 422,   // INVALID_FORM_INPUTS
            3       => 401,   // INVALID_CREDENTIALS
            4       => 401,   // NOT_LOGGED_IN
            5       => 422,   // RECORD_ALREADY_EXIST
            6       => 404,   // RECORD_NOT_EXIST
            9       => 403,   // ACCESS_DENIED
            10      => 401,   // EMAIL_NOT_VERIFIED
            default => 500,   // INTERNAL_SERVER_ERROR
        };
    }
}
