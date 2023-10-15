<?php
declare(strict_types=1);

namespace App\Models\Users;

use App\Builders\UserBuilder;
use App\Interfaces\HashCheckable;
use App\Interfaces\Historical;
use App\Interfaces\Statusable;
use App\Models\Positions\Position;
use App\Traits\HashCheck;
use App\Traits\HasHistory;
use App\Traits\HasStatus;
use App\Traits\SetAttributeWithChanges;
use App\Utils\Name;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property int $status_id
 * @property boolean $locked
 *
 * @property string $email
 * @property string|null $username
 * @property string|null $phone
 * @property string $password
 *
 * @property string|null $lastname
 * @property string|null $firstname
 * @property string|null $patronymic
 *
 * @property string|null $display_name
 *
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $phone_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property string|null $remember_token
 *
 * @property UserStatus $status
 * @property UserInfo $info
 * @property Collection<Position> $positions
 *
 * @property-read string|null $fullName
 * @property-read string|null $compactName
 */
class User extends Authenticatable implements Statusable, Historical, HashCheckable
{
    use HasApiTokens, HasFactory, HasStatus, HashCheck, HasHistory, SetAttributeWithChanges;

    /** @var string Referenced table. */
    protected $table = 'users';

    /** @var string[] Attribute casting. */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'locked' => 'bool',
    ];

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'email',
        'username',
        'phone',
        'password',
        'lastname',
        'firstname',
        'patronymic',
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

    /** @var array The accessors to append to the model's array. */
    protected $appends = ['fullName', 'compactName'];

    /**
     * Begin querying the model.
     *
     * @return UserBuilder
     */
    public static function query(): UserBuilder
    {
        /** @var UserBuilder $query */
        $query = parent::query();

        return $query;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param Builder $query
     *
     * @return UserBuilder
     */
    public function newEloquentBuilder($query): UserBuilder
    {
        return new UserBuilder($query);
    }

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

    /**
     * Accessor for full name generation.
     *
     * @return  string|null
     */
    public function getFullNameAttribute(): ?string
    {
        return Name::full($this->lastname, $this->firstname, $this->patronymic);
    }

    /**
     * Accessor for compact name generation.
     *
     * @return  string|null
     */
    public function getCompactNameAttribute(): ?string
    {
        return Name::compact($this->lastname, $this->firstname, $this->patronymic);
    }

    /**
     * History entry title.
     *
     * @return  string
     */
    public function historyEntryCaption(): string
    {
        return $this->compactName;
    }
}
