<?php

namespace App\Models\User;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $lastname
 * @property string $firstname
 * @property string $patronymic
 * @property string $gender
 * @property Carbon $birthdate
 * @property string $mobile_phone
 * @property string $email
 * @property string $notes
 *
 * @property User $user
 *
 * @property-read string|null $fullName
 * @property-read string|null $compactName
 */
class UserInfo extends Model
{
    use HasFactory;

    /** @var string Referenced table. */
    protected $table = 'user_info';

    /** @var string The primary key associated with the table. */
    protected $primaryKey = 'user_id';

    /** @var bool Disable auto-incrementing on model. */
    public $incrementing = false;

    /** @var string[] Attributes casting */
    protected $casts = [
        'birthdate' => 'date',
    ];

    /** @var array The accessors to append to the model's array. */
    protected $appends = ['fullName', 'compactName'];

    /** @var string[] Fillable attributes. */
    protected $fillable = [
        'lastname',
        'firstname',
        'patronymic',
        'birthdate',
        'gender',
        'mobile_phone',
        'email',
        'notes',
    ];

    /**
     * User this profile belongs to.
     *
     * @return  BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
