<?php

namespace App\Models\Users;

use App\Interfaces\Statusable;
use App\Models\Positions\Position;
use App\Traits\HasStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int $status_id
 * @property string $username
 * @property string $password
 * @property string $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property UserStatus $status
 * @property UserInfo $info
 */
class User extends Authenticatable implements Statusable
{
    use HasApiTokens, HasFactory, HasStatus;

    /** @var string Referenced table. */
    protected $table = 'users';

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'username',
        'password',
    ];

    /** @var array The attributes that should be hidden for serialization. */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array Default attributes. */
    protected $attributes = [
        'status_id' => UserStatus::default,
    ];

    /**
     * User's status.
     *
     * @return  HasOne
     */
    public function status(): HasOne
    {
        return $this->hasOne(UserStatus::class, 'id', 'status_id');
    }

    /**
     * Check and set new status for user.
     *
     * @param int $status
     * @param bool $save
     *
     * @return  void
     */
    public function setStatus(int $status, bool $save = false): void
    {
        $this->checkAndSetStatus(UserStatus::class, $status, 'status_id', $save);
    }

    /**
     * Positions of user.
     *
     * @return  HasMany
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'user_id', 'id');
    }

    /**
     * User's profile.
     *
     * @return  HasOne
     */
    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id')->withDefault();
    }
}
