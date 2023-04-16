<?php
declare(strict_types=1);

namespace App\Models\Users;

use App\Current;
use App\Exceptions\Model\ModelLockedException;
use App\Exceptions\Model\ModelNotFoundException;
use App\Exceptions\Model\ModelWrongHashException;
use App\Interfaces\HashCheckable;
use App\Interfaces\Historical;
use App\Interfaces\Statusable;
use App\Models\EntryScope;
use App\Models\History\HistoryAction;
use App\Models\History\HistoryChanges;
use App\Models\Positions\Position;
use App\Traits\HashCheck;
use App\Traits\HasHistoryLine;
use App\Traits\HasStatus;
use App\Traits\SetAttributeWithChanges;
use App\Utils\Casting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
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
 * @property Collection<Position> $positions
 *
 * @property-read string|null $fullName
 * @property-read string|null $compactName
 */
class User extends Authenticatable implements Statusable, Historical, HashCheckable
{
    use HasApiTokens, HasFactory, HasStatus, HashCheck, HasHistoryLine, SetAttributeWithChanges;

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
     * User's status.
     *
     * @return  HasOne
     * @noinspection PhpUnused
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
     * @noinspection PhpUnused
     */
    public function info(): HasOne
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id')->withDefault();
    }

    /**
     * Accessor for full name generation.
     *
     * @return  string|null
     * @noinspection PhpUnused
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->getFullName();
    }

    /**
     * Accessor for compact name generation.
     *
     * @return  string|null
     * @noinspection PhpUnused
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
        return $this->updated_at?->toString();
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
     * Get permission group.
     *
     * @param int|null $id
     * @param string|null $hash
     * @param bool $check
     * @param bool $onlyExisting
     *
     * @return User
     * @throws ModelLockedException
     * @throws ModelWrongHashException
     * @throws ModelNotFoundException
     */
    public static function get(?int $id, ?string $hash = null, bool $check = false, bool $onlyExisting = true): User
    {
        /** @var User|null $user */
        if ($id === null) {
            $user = $onlyExisting ? null : new self();
        } else {
            $user = self::query()->where('id', $id)->first();
        }

        if ($user === null) {
            throw new ModelNotFoundException('Учётная запись не найдена');
        }
        if ($check && $user->exists && !$user->isHash($hash)) {
            throw new ModelWrongHashException('Учётная запись была изменена в другом месте.');
        }
        if ($user->locked) {
            throw new ModelLockedException('Эту учётную запись нельзя изменить или удалить');
        }

        return $user;
    }

    /**
     * Update user common data.
     *
     * @param string|null $lastname
     * @param string|null $firstname
     * @param string|null $patronymic
     * @param string|null $displayName
     * @param string|null $username
     * @param string|null $phone
     * @param Current $current
     *
     * @return $this
     */
    public function change(?string $lastname, ?string $firstname, ?string $patronymic, ?string $displayName, ?string $username, ?string $phone, Current $current): self
    {
        $changes = [];
        $changes[] = $this->changeAttribute('lastname', $lastname, Casting::string);
        $changes[] = $this->changeAttribute('firstname', $firstname, Casting::string);
        $changes[] = $this->changeAttribute('patronymic', $patronymic, Casting::string);
        $changes[] = $this->changeAttribute('display_name', $displayName, Casting::string);
        $changes[] = $this->changeAttribute('username', $username, Casting::string);
        $changes[] = $this->changeAttribute('phone', $phone, Casting::string);
        $this->save();

        $changes = array_filter($changes);

        if (!empty($changes)) {
            $this
                ->addHistory($this->wasRecentlyCreated ? HistoryAction::user_created : HistoryAction::user_edited, $current->positionId())
                ->addChanges($changes);
        }

        return $this;
    }

    /**
     * Change user status.
     *
     * @param int $statusId
     * @param Current $current
     *
     * @return $this
     */
    public function changeStatus(int $statusId, Current $current): self
    {
        if ($statusId !== $this->status_id) {
            $this->setStatus($statusId);
            $this->save();

            $action = match ($this->status_id) {
                UserStatus::active => HistoryAction::user_activated,
                UserStatus::blocked => HistoryAction::user_deactivated,
            };
            $this->addHistory($action, $current->positionId());
        }

        return $this;
    }

    /**
     * Change user password.
     *
     * @param string|null $newPassword
     * @param bool $clear
     * @param Current $current
     *
     * @return $this
     */
    public function changePassword(?string $newPassword, bool $clear, Current $current): self
    {
        if ($clear) {
            if (!empty($this->password)) {
                $this->password = null;
                $this->save();
                $this->addHistory(HistoryAction::user_password_cleared, $current->positionId());
            }
        } else if (!empty($newPassword)) {
            $action = match (true) {
                empty($this->password) => HistoryAction::user_password_set,
                default => HistoryAction::user_password_changed,
            };
            $this->password = Hash::make($newPassword);
            $this->save();
            $this->addHistory($action, $current->positionId());
        }

        return $this;
    }

    public function changeEmail(?string $newEmail, bool $withConfirmation, Current $current): self
    {
        if ($this->email === $newEmail) {
            return $this;
        }

        if (!$withConfirmation || $newEmail === null) {
            $oldEmail = $this->email;
            $change = $this->changeAttribute('email', $newEmail, Casting::string);
            $this->save();

            $action = match (true) {
                ($oldEmail === null) => HistoryAction::user_email_set,
                ($newEmail === null) => HistoryAction::user_email_cleared,
                default => HistoryAction::user_email_changed,
            };

            $this
                ->addHistory($action, $current->positionId())
                ->addChanges([$change]);

            return $this;
        }

        $confirmation = UserEmailConfirmation::create($this, $newEmail);

        // TODO send mail
        $this->addHistory(HistoryAction::user_email_verification_sent, $current->positionId(), $newEmail);

        return $this;
    }

    /**
     * Remove user.
     *
     * @param Current $current
     *
     * @return void
     */
    public function remove(Current $current): void
    {
        $changes = [
            new HistoryChanges(['parameter' => 'lastname', 'type' => Casting::string, 'old' => $this->lastname, 'new' => null]),
            new HistoryChanges(['parameter' => 'firstname', 'type' => Casting::string, 'old' => $this->firstname, 'new' => null]),
            new HistoryChanges(['parameter' => 'patronymic', 'type' => Casting::string, 'old' => $this->patronymic, 'new' => null]),
            new HistoryChanges(['parameter' => 'display_name', 'type' => Casting::string, 'old' => $this->display_name, 'new' => null]),
            new HistoryChanges(['parameter' => 'email', 'type' => Casting::string, 'old' => $this->email, 'new' => null]),
            new HistoryChanges(['parameter' => 'phone', 'type' => Casting::string, 'old' => $this->phone, 'new' => null]),
            new HistoryChanges(['parameter' => 'username', 'type' => Casting::string, 'old' => $this->username, 'new' => null]),
        ];

        $this
            ->addHistory(HistoryAction::user_deleted, $current->positionId())
            ->addChanges($changes);

        $this->delete();
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
            'locked' => $this->locked,
            'status' => $this->status->name,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'patronymic' => $this->patronymic,
            'display_name' => $this->display_name,
            'username' => $this->username,
            'email' => $this->email,
            'has_password' => !empty($this->password),
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'hash' => $this->getHash(),
        ];
    }
}
