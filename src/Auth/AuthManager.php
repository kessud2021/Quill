<?php

namespace Framework\Auth;

use Framework\Database\Model;

/**
 * Authentication manager
 */
class AuthManager
{
    /**
     * Authenticated user
     *
     * @var Model|null
     */
    protected ?Model $user = null;

    /**
     * Guard name
     *
     * @var string
     */
    protected string $guard = 'web';

    /**
     * User model class
     *
     * @var string
     */
    protected string $userModel = 'App\\Models\\User';

    /**
     * Create a new auth manager
     */
    public function __construct()
    {
        $this->loadUserFromSession();
    }

    /**
     * Load user from session
     *
     * @return void
     */
    protected function loadUserFromSession(): void
    {
        $userId = session()->get('auth.user.id');

        if ($userId) {
            $modelClass = $this->userModel;
            $this->user = $modelClass::find($userId);
        }
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->user !== null;
    }

    /**
     * Get the authenticated user
     *
     * @return Model|null
     */
    public function user(): ?Model
    {
        return $this->user;
    }

    /**
     * Get the authenticated user ID
     *
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->user?->id;
    }

    /**
     * Authenticate a user
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return bool
     */
    public function attempt(string $email, string $password, bool $remember = false): bool
    {
        $modelClass = $this->userModel;
        $user = $modelClass::findBy('email', $email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $this->login($user);
        return true;
    }

    /**
     * Log in a user
     *
     * @param Model $user
     * @return void
     */
    public function login(Model $user): void
    {
        $this->user = $user;
        session()->put('auth.user.id', $user->id);
    }

    /**
     * Log out the user
     *
     * @return void
     */
    public function logout(): void
    {
        $this->user = null;
        session()->forget('auth.user.id');
    }
}
