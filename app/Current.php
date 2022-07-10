<?php

namespace App;

use App\Models\User\User;
use Illuminate\Http\Request;

class Current
{
    /** @var User this helper for. */
    protected User $user;

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
     * @param User $user
     *
     * @return  void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
     * Get current username.
     *
     * @return  string|null
     */
    public function userName(): ?string
    {
        return isset($this->user) ? $this->user->info->compactName : null;
    }
}
