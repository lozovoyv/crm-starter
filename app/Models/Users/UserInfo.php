<?php
declare(strict_types=1);

namespace App\Models\Users;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property string $gender
 * @property Carbon $birthdate
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
        'birthdate',
        'gender',
        'notes',
    ];

    /**
     * User this info belongs to.
     *
     * @return  BelongsTo
     * @noinspection PhpUnused
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
