<?php

namespace App\Models\Users;

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
     * User this info belongs to.
     *
     * @return  BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
