<?php

namespace App\Services;

use App\Interfaces\AuthServiceInterface;
use App\libs\Response\GlobalApiResponse;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Class AuthService
 *
 * Each public method is the orchestrator — it calls small,
 * focused private helpers that each do exactly one thing.
 * This makes the code easy to read, test, and change.
 *
 * @package App\Services
 */
class AuthService implements AuthServiceInterface
{
    use ApiResponseTrait;

    /**
     * Register a new user and send a verification e-mail.
     */
    public function register(array $data): GlobalApiResponse
    {
        try {
            if ($this->emailAlreadyTaken($data['email'])) {
                return $this->alreadyExists('An account with this email already exists.');
            }

            $user = $this->createUser($data);
            $this->sendVerificationEmail($user);

            return $this->success(
                'Registration successful! Please check your inbox and verify your email address.',
                ['user' => $user->toPublicArray()]
            );
        } catch (\Throwable $e) {
            return $this->serverError('AuthService@register', $e, 'Registration failed. Please try again later.');
        }
    }

    /**
     * Verify the user's email using the one-time code.
     */
    public function verifyEmail(string $code): GlobalApiResponse
    {
        try {
            $user = $this->findUserByVerificationCode($code);

            if (!$user) {
                return $this->notFound('This verification link is invalid or has already been used.');
            }

            if ($user->is_verified) {
                return $this->success('Your email is already verified. You can log in now.');
            }

            $user->markAsVerified();

            return $this->success('Email verified successfully! You can now log in.');
        } catch (\Throwable $e) {
            return $this->serverError('AuthService@verifyEmail', $e, 'Verification failed. Please try again later.');
        }
    }

    /**
     * Authenticate credentials and return a JWT token.
     */
    public function login(array $credentials): GlobalApiResponse
    {
        try {
            $token = $this->attemptLogin($credentials);

            if (!$token) {
                return $this->invalidCredentials('The email or password you entered is incorrect.');
            }

            /** @var User $user */
            $user = Auth::user();

            if ($user->isNotVerified()) {
                Auth::forgetUser();
                return $this->emailNotVerified('Please verify your email address before logging in.');
            }

            return $this->success(
                'Logged in successfully.',
                $this->buildTokenPayload($user, $token)
            );
        } catch (\Throwable $e) {
            return $this->serverError('AuthService@login', $e, 'Login failed. Please try again later.');
        }
    }

    /**
     * Invalidate the current JWT token.
     */
    public function logout(): GlobalApiResponse
    {
        try {
            $this->invalidateToken();

            return $this->success('You have been logged out successfully.');
        } catch (TokenInvalidException) {
            // Token already invalid — still a success from the client's view
            return $this->success('You have been logged out successfully.');
        } catch (\Throwable $e) {
            return $this->serverError('AuthService@logout', $e, 'Logout failed. Please try again.');
        }
    }

    /**
     * Check if an email address is already registered.
     */
    private function emailAlreadyTaken(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * Persist a new user record.
     * Password is auto-hashed via the model cast.
     */
    private function createUser(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);
    }

    /**
     * Generate a verification code and fire the notification.
     */
    private function sendVerificationEmail(User $user): void
    {
        $code = $user->generateVerificationCode();
        $user->notify(new VerifyEmailNotification($code));
    }

    /**
     * Find a user by their one-time verification code.
     * Returns null if no match — callers decide what to do.
     */
    private function findUserByVerificationCode(string $code): ?User
    {
        return User::where('verification_code', $code)->first();
    }

    /**
     * Attempt authentication and return the JWT token string.
     * Returns false if credentials are wrong.
     */
    private function attemptLogin(array $credentials): string|false
    {
        return Auth::attempt($credentials);
    }

    /**
     * Build the token response payload returned to the client.
     */
    private function buildTokenPayload(User $user, string $token): array
    {
        return [
            'user'         => $user->toPublicArray(),
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $this->getTokenTTL(),
        ];
    }

    /**
     * Get token expiry in seconds.
     */
    private function getTokenTTL(): int
    {
        return JWTAuth::factory()->getTTL() * 60;
    }

    /**
     * Blacklist the current JWT so it cannot be reused.
     */
    private function invalidateToken(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
}
