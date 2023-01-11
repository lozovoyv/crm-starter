<?php

namespace App;

use App\Models\Positions\Position;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Current
{
    protected ?User $user;

    protected ?Position $position;

    /**
     * Factory.
     *
     * @param Request $request
     *
     * @return  Current
     */
    public static function get(Request $request): Current
    {
        return new Current($request);
    }

    /**
     * Create user current state.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->user = $request->user();
        if ($this->user) {
            $positionId = $request->session()->get('position_id');
            if ($positionId !== null) {
                // todo add cookie position handling
                // todo improve position checkin and overriding (by permission or role)
                $this->position = $this->user->positions()->where('id', $positionId)->first();
                $request->session()->put('position_id', $this->position->id);
            } else {
                if ($this->user->positions()->count() === 1) {
                    $this->position = $this->user->positions()->first();
                } else {
                    // todo throw `need position select` exception
                    $this->position = $this->user->positions()->first();
                }
            }
        } else {
            $this->position = null;
        }
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
        return isset($this->user) ? $this->user->compactName : null;
    }

    /**
     * Get current user email.
     *
     * @return  string|null
     */
    public function email(): ?string
    {
        return isset($this->user) ? $this->user->email : null;
    }

    public function positionId(): ?int
    {
        return $this->position ? $this->position->id : null;
    }

    /**
     * Get current user permissions.
     *
     * @return  array
     */
    public function permissions(): array
    {
        return $this->position ? $this->position->getPermissionsList() : [];
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
        return $this->position ? $this->position->can($key, $fresh) : false;
    }

    /**
     * Get current user permissions.
     *
     * @return Collection|null
     */
    public function roles(): ?Collection
    {
        return $this->position ? $this->position->roles : null;
    }

    /**
     * Check current user permission.
     *
     * @param int|string $role
     * @param bool $fresh
     *
     * @return  bool
     */
    public function hasRole(int|string $role, bool $fresh = false): bool
    {
        return $this->position ? $this->position->hasRole($role, $fresh) : false;
    }

    /**
     * Check current user position matches the given type.
     *
     * @param int $positionType
     *
     * @return  bool
     */
    public function hasPositionType(int $positionType): bool
    {
        return $this->position && $this->position->type_id === $positionType;
    }
}
