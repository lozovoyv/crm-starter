<?php
declare(strict_types=1);

namespace App\Models\Users;

use App\Actions\Users\UserEmailSetConfirmedAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * @property int $id
 * @property int $user_id
 * @property string $new_email
 * @property string $token
 * @property Carbon $expires_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 */
class UserEmailConfirmation extends Model
{
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create new user email confirmation.
     *
     * @param User $user
     * @param string $newEmail
     *
     * @return static
     */
    public static function create(User $user, string $newEmail): self
    {
        self::query()->where('user_id', $user->id)->delete();

        $emailConfirmation = new self();
        $emailConfirmation->user_id = $user->id;
        $emailConfirmation->new_email = $newEmail;

        $lifeTime = config('auth.email_confirmation_timeout');
        if ($lifeTime > 0) {
            $emailConfirmation->expires_at = Carbon::now()->addMinutes($lifeTime);
        }

        DB::transaction(static function () use ($emailConfirmation) {
            $token = null;
            while ($token === null) {
                $token = md5(Carbon::now()->toISOString());
                if (self::query()->where('token', $token)->count() > 0) {
                    $token = null;
                }
            }
            $emailConfirmation->token = $token;
            $emailConfirmation->save();
        });

        return $emailConfirmation;
    }

    /**
     * User this info belongs to.
     *
     * @return  BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate confirmation link.
     *
     * @return string
     */
    public function getConfirmationLink(): string
    {
        return url('/confirm/email?') . Arr::query(['token' => $this->token]);
    }

    /**
     * Apply new email to user.
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function applyNewEmail(): void
    {
        $this->loadMissing('user');
        $action = new UserEmailSetConfirmedAction();
        $action->execute($this->user, $this->new_email);
        $this->delete();
    }
}
