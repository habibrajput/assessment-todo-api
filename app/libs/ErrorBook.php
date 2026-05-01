<?php

namespace App\libs;

/**
 * Class ErrorBook
 *
 * Simple numeric constants used when checking whether a response
 * was successful without caring about the full outcome string.
 *
 * @package App\libs
 */
class ErrorBook
{
    const API_SUCCESS        = 0;
    const API_FORM_ERRORS    = 2;
    const API_USER_NOT_LOGGED_IN = 4;
    const API_SERVER         = 8;
}
