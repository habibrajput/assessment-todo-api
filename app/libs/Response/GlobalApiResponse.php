<?php

namespace App\libs\Response;

use App\libs\ErrorBook;
use App\libs\Response\GlobalApiResponseCodeBook;

/**
 * Class GlobalApiResponse
 *
 * Generates a standardized JSON response structure for all API endpoints.
 * Every response — success or error — flows through this class so the
 * frontend always receives a predictable shape.
 *
 * @package App\libs\Response
 */
class GlobalApiResponse implements \JsonSerializable
{
    private string $outcome    = 'SUCCESS';
    private int    $outcomeCode = 0;
    private string $message    = '';
    private int    $numOfRecords = 0;
    private mixed  $records;
    private array  $errors     = [];

    // Getters / Setters

    public function getMessage(): string
    {
        return $this->message;
    }
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function getOutcome(): string
    {
        return $this->outcome;
    }
    public function setOutcome(string $outcome): void
    {
        $this->outcome = $outcome;
    }

    public function getOutcomeCode(): int
    {
        return $this->outcomeCode;
    }
    public function setOutcomeCode(int $code): void
    {
        $this->outcomeCode = $code;
    }

    public function getNumOfRecords(): int
    {
        return $this->numOfRecords;
    }
    public function setNumOfRecords(int $num): void
    {
        $this->numOfRecords = $num;
    }

    public function getRecords(): mixed
    {
        return $this->records;
    }
    public function setRecords(mixed $records): void
    {
        $this->records = $records;
    }

    // Core builder

    /**
     * Internal method that actually populates all fields.
     */
    private function setResponse(
        string $outcome,
        int    $outcomeCode,
        string $message,
        int    $numOfRecords = 0,
        mixed  $records      = [],
        array  $errors       = []
    ): static {
        $this->outcome      = $outcome;
        $this->outcomeCode  = $outcomeCode;
        $this->message      = $message;
        $this->numOfRecords = $numOfRecords;
        $this->records      = $records ?: new \stdClass();
        $this->errors       = $errors;

        return $this;
    }

    // Public helpers

    /**
     * Build a success response.
     *
     * @param  string  $message
     * @param  int     $numOfRecords   Total count of returned records
     * @param  mixed   $records        The actual payload (array, object, …)
     * @return static
     */
    public function success(string $message = '', int $numOfRecords = 0, mixed $records = []): static
    {
        $this->setResponse(
            GlobalApiResponseCodeBook::SUCCESS['outcome'],
            GlobalApiResponseCodeBook::SUCCESS['outcomeCode'],
            $message,
            $numOfRecords,
            $records ?: new \stdClass(),
            []
        );

        return $this;
    }

    /**
     * Build an error response.
     *
     * @param  array   $outcomeArray   One of the constants from GlobalApiResponseCodeBook
     * @param  string  $message        Human-readable description
     * @param  array   $errors         Validation / field-level errors
     * @return static
     */
    public function error(array $outcomeArray, string $message, array $errors = []): static
    {
        $this->setResponse(
            $outcomeArray['outcome'],
            $outcomeArray['outcomeCode'],
            $message,
            0,
            new \stdClass(),
            $errors
        );

        return $this;
    }

    // JsonSerializable ──────────────────────────────────────────────────────

    /**
     * Shape that json_encode() / response()->json() will emit.
     */
    public function jsonSerialize(): mixed
    {
        return [
            '_metadata' => [
                'outcome'      => $this->outcome,
                'outcomeCode'  => $this->outcomeCode,
                'numOfRecords' => $this->numOfRecords,
                'message'      => $this->message,
            ],
            'records' => $this->records ?: new \stdClass(),
            'errors'  => $this->errors,
        ];
    }

    /**
     * Convenience check: did this response represent a success?
     */
    public function isSuccess(): bool
    {
        return $this->outcomeCode === ErrorBook::API_SUCCESS;
    }
}
