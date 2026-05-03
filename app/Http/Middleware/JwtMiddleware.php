<?php

namespace App\Http\Middleware;

use App\libs\Response\GlobalApiResponse;
use App\libs\Response\GlobalApiResponseCodeBook;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorised('User not found.');
            }
        } catch (TokenExpiredException $e) {
            return $this->unauthorised('Your session has expired. Please log in again.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorised('The token provided is invalid.');
        } catch (JWTException $e) {
            return $this->unauthorised('No authentication token was provided.');
        }

        return $next($request);
    }

    private function unauthorised(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            (new GlobalApiResponse())->error(
                GlobalApiResponseCodeBook::NOT_LOGGED_IN,
                $message
            ),
            401
        );
    }
}
