<?php

namespace App;

use App\Models\User\User;
use Illuminate\Http\Request;

class Current
{
    /** @var User|null This helper for. */
    protected ?User $user;

    /**
     * Factory.
     *
     * @param Request $request
     *
     * @return  Current
     */
    public static function get(Request $request): Current
    {
        return new Current($request->user());
    }

    /**
     * Create user current state.
     *
     * @param User|null $user
     *
     * @return  void
     */
    public function __construct(?User $user)
    {
        $this->user = $user;
    }

    /**
     * Weather user authenticated.
     *
     * @return  bool
     */
    public function isAuthenticated(): bool
    {
        return isset($this->user);
    }

    /**
     * Get current user ID.
     *
     * @return  int|null
     */
    public function userId(): ?int
    {
        return isset($this->user) ? $this->user->id : null;
    }

    /**
     * Get current username.
     *
     * @return  string|null
     */
    public function userName(): ?string
    {
        return isset($this->user) ? $this->user->info->compactName : null;
    }

    /**
     * Get current user permissions.
     *
     * @return  array
     */
    public function permissions(): array
    {
        return [];
    }

    /**
     * Check current user permission.
     *
     * @param string|null $key
     * @param bool $fresh
     *
     * @return  bool
     */
    public function can(?string $key, bool $fresh = false): bool
    {
        return true;
    }

    /**
     * Get current user permissions.
     *
     * @return  array
     */
    public function roles(): array
    {
        return [];
    }

    /**
     * Check current user permission.
     *
     * @param string|int $role
     * @param bool $fresh
     *
     * @return  bool
     */
    public function hasRole(string | int $role, bool $fresh = false): bool
    {
        return true;
    }
}
