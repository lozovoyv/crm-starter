<?php

namespace App\Models\Users;

use App\Interfaces\Historical;
use App\Interfaces\Statusable;
use App\Models\EntryScope;
use App\Models\Positions\Position;
use App\Traits\HashCheck;
use App\Traits\HasHistoryLine;
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
 * @property Carbon $email_verified_at
 * @property Carbon $phone_verified_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property string $remember_token
 *
 * @property int|null $history_line_id
 *
 * @property UserStatus $status
 * @property UserInfo $info
 *
 * @property-read string|null $fullName
 * @property-read string|null $compactName
 */
class User extends Authenticatable implements Statusable, Historical
{
    use HasApiTokens, HasFactory, HasStatus, HashCheck, HasHistoryLine;

    /** @var string Referenced table. */
    protected $table = 'users';

    /** @var string[] Attribute casting. */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->getFullName();
    }

    /**
     * Accessor for compact name generation.
     *
     * @return  string|null
     */
    public function getCompactNameAttribute(): ?string
    {
        return $this->getCompactName();
    }

    /**
     * Return lastname.
     *
     * @return  string|null
     */
    public function getLastName(): ?string
    {
        return $this->format('lastname', true);
    }

    /**
     * Return firstname in full or short mode.
     *
     * @param bool $full
     *
     * @return  string|null
     */
    public function getFirstName(bool $full = true): ?string
    {
        return $this->format('firstname', $full);
    }

    /**
     * Return patronymic in full or short mode.
     *
     * @param bool $full
     *
     * @return  string|null
     */
    public function getPatronymic(bool $full = true): ?string
    {
        return $this->format('patronymic', $full);
    }

    /**
     * Return fathers name in full or short mode.
     *
     * @return  string|null
     */
    public function getFullName(): ?string
    {
        $value = trim(implode(' ', [$this->getLastName(), $this->getFirstName(), $this->getPatronymic()]));

        return empty($value) ? null : $value;
    }

    /**
     * Return fathers name in full or short mode.
     *
     * @return  string|null
     */
    public function getCompactName(): ?string
    {
        $value = trim(sprintf('%s %s%s', $this->getLastName(), $this->getFirstName(false), $this->getPatronymic(false)));

        return empty($value) ? null : $value;
    }

    /**
     * Format string in full or short mode.
     *
     * @param bool $full
     * @param string|null $attribute
     *
     * @return  string|null
     */
    protected function format(?string $attribute, bool $full): ?string
    {
        $value = $this->getAttribute($attribute);
        if (empty($value)) {
            return null;
        }

        return $full ? mb_strtoupper(mb_substr($value, 0, 1)) . mb_strtolower(mb_substr($value, 1)) : mb_strtoupper(mb_substr($value, 0, 1)) . '.';
    }

    /**
     * Instance hash.
     *
     * @return  string|null
     */
    public function hash(): ?string
    {
        return $this->updated_at;
    }

    /**
     * History entry title.
     *
     * @return  string
     */
    public function historyEntryTitle(): string
    {
        return $this->compactName;
    }

    /**
     * History entry name.
     *
     * @return  string
     */
    public function historyEntryName(): string
    {
        return EntryScope::user;
    }

    /**
     * History entry name.
     *
     * @return  string|null
     */
    public function historyEntryType(): ?string
    {
        return null;
    }

    /**
     * Cast to array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'is_active' => $this->hasStatus(UserStatus::active),
            'status' => $this->status->name,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'patronymic' => $this->patronymic,
            'display_name' => $this->display_name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'hash' => $this->getHash(),
        ];
    }
}
