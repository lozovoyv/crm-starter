<?php
declare(strict_types=1);

namespace App;

use App\Exceptions\NoPositionSelectedException;
use App\Exceptions\PositionMismatchException;
use App\Models\Positions\Position;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Current
{
    protected static ?self $instance;

    protected ?User $user;

    protected ?Position $position;
    protected ?Position $proxyPosition;

    /**
     * Factory with singleton binding.
     *
     * @param Request $request
     * @param bool $refresh
     *
     * @return  Current
     */
    public static function init(Request $request, bool $refresh = false): Current
    {
        if (!isset(static::$instance) || $refresh) {
            static::$instance = new Current($request);
        }

        return static::$instance;
    }

    /**
     * Reset instance.
     *
     * @return void
     */
    public static function unset(): void
    {
        self::$instance = null;
    }

    /**
     * Create user current state.
     *
     * @param Request $request
     *
     * @throws NoPositionSelectedException
     */
    public function __construct(Request $request)
    {
        $this->user = $request->user();

        if ($this->user === null) {
            $this->position = null;
            return;
        }

        $positionId = $request->session()->get('position_id');

        if ($positionId !== null) {
            $this->position = $this->user->positions()->where('id', $positionId)->first();
            $request->session()->put('position_id', $position->id ?? null);
            if (!$this->position) {
                throw new PositionMismatchException();
            }

        } else {
            $positionsCount = $this->user->positions()->count();

            if ($positionsCount === 1) {
                $this->position = $this->user->positions()->first();
                $request->session()->put('position_id', $this->position->id);

            } else if ($positionsCount > 0) {
                throw new NoPositionSelectedException();

            } else {
                $this->position = null;
                $request->session()->forget('position_id');
            }
        }

        if ($this->position && ($proxyId = $request->cookie('proxy_position')) && $this->position->can('system.act_as_other')) {
            $this->proxyPosition = Position::query()->where('id', $proxyId)->first();
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
     * Get current user.
     *
     * @return User|null
     */
    public function user(): ?User
    {
        return $this->user ?? null;
    }

    /**
     * Get current user position.
     *
     * @return Position|null
     */
    public function position(): ?Position
    {
        return $this->proxyPosition ?? $this->position ?? null;
    }

    /**
     * Get current position ID.
     *
     * @return int|null
     */
    public function positionId(): ?int
    {
        return $this->proxyPosition->id ?? $this->position->id ?? null;
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
    public function userEmail(): ?string
    {
        return $this->user->email ?? null;
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
