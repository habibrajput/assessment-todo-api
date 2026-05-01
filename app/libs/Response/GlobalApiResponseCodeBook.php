<?php

namespace App\libs\Response;

/**
 * Class GlobalApiResponseCodeBook
 *
 * Central registry of every outcome code used across the API.
 * Keeping them here means one place to change if codes ever shift.
 *
 * @package App\libs\Response
 */
class GlobalApiResponseCodeBook
{
    const SUCCESS = [
        'outcome'     => 'SUCCESS',
        'outcomeCode' => 0,
    ];

    const NOT_AUTHORIZED = [
        'outcome'     => 'NOT_AUTHORIZED',
        'outcomeCode' => 1,
    ];

    const INVALID_FORM_INPUTS = [
        'outcome'     => 'INVALID_FORM_INPUTS',
        'outcomeCode' => 2,
    ];

    const INVALID_CREDENTIALS = [
        'outcome'     => 'INVALID_CREDENTIALS',
        'outcomeCode' => 3,
    ];

    const NOT_LOGGED_IN = [
        'outcome'     => 'NOT_LOGGED_IN',
        'outcomeCode' => 4,
    ];

    const RECORD_ALREADY_EXIST = [
        'outcome'     => 'RECORD_ALREADY_EXIST',
        'outcomeCode' => 5,
    ];

    const RECORD_NOT_EXIST = [
        'outcome'     => 'RECORD_NOT_EXIST',
        'outcomeCode' => 6,
    ];

    const FILE_NOT_EXIST = [
        'outcome'     => 'FILE_NOT_EXIST',
        'outcomeCode' => 7,
    ];

    const INTERNAL_SERVER_ERROR = [
        'outcome'     => 'INTERNAL_SERVER_ERROR',
        'outcomeCode' => 8,
    ];

    const ACCESS_DENIED = [
        'outcome'     => 'ACCESS_DENIED',
        'outcomeCode' => 9,
    ];

    const EMAIL_NOT_VERIFIED = [
        'outcome'     => 'EMAIL_NOT_VERIFIED',
        'outcomeCode' => 10,
    ];
}
