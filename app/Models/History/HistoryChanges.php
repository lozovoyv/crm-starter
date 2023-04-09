<?php
declare(strict_types=1);

namespace App\Models\History;

use App\Models\EntryScope;
use App\Models\History\Formatters\FormatterInterface;
use App\Models\History\Formatters\PermissionRoleChangesFormatter;
use App\Models\History\Formatters\PositionChangesFormatter;
use App\Models\History\Formatters\UserChangesFormatter;
use App\Utils\Casting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use JsonException;

/**
 * @property int $id
 * @property int $history_id
 * @property string $parameter
 * @property int $type
 * @property mixed|null $old
 * @property mixed|null $new
 */
class HistoryChanges extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property of related history record. */
    public $timestamps = false;

    /** @var string[] Fillable attributes. */
    protected $fillable = ['parameter', 'type', 'old', 'new'];

    protected array $formatters = [
        EntryScope::role => PermissionRoleChangesFormatter::class,
        EntryScope::position => PositionChangesFormatter::class,
        EntryScope::user => UserChangesFormatter::class,
    ];

    /**
     * Convert old value from store value to real type.
     *
     * @param string|null $value
     *
     * @return  string|array|bool|int|Carbon|null
     * @noinspection PhpUnused
     */
    public function getOldAttribute(?string $value): string|array|bool|int|null|Carbon
    {
        try {
            return $value !== null ? Casting::fromString($value, $this->type) : null;
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Convert old value to store value.
     *
     * @param string|array|bool|int|Carbon|null $value
     *
     * @return  void
     * @noinspection PhpUnused
     */
    public function setOldAttribute(string|array|bool|int|null|Carbon $value): void
    {
        try {
            $this->attributes['old'] = $value !== null ? Casting::toString($value, $this->type) : null;
        } catch (JsonException) {
            return;
        }
    }

    /**
     * Convert new value from store value to real type.
     *
     * @param string|null $value
     *
     * @return  string|array|bool|int|Carbon|null
     * @noinspection PhpUnused
     */
    public function getNewAttribute(?string $value): string|array|bool|int|null|Carbon
    {
        try {
            return $value !== null ? Casting::fromString($value, $this->type) : null;
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * Convert new value to store value.
     *
     * @param string|array|bool|int|Carbon|null $value
     *
     * @return  void
     * @noinspection PhpUnused
     */
    public function setNewAttribute(string|array|bool|int|null|Carbon $value): void
    {
        try {
            $this->attributes['new'] = $value !== null ? Casting::toString($value, $this->type) : null;
        } catch (JsonException) {
            return;
        }
    }

    /**
     * Cast to array using formatter.
     *
     * @param string|null $forScope
     *
     * @return array
     */
    public function toArray(?string $forScope = null): array
    {
        if ($forScope === null || !isset($this->formatters[$forScope])) {
            return [
                'parameter' => $this->parameter,
                'old' => $this->old,
                'new' => $this->new,
            ];
        }

        /** @var FormatterInterface $formatter */
        $formatter = $this->formatters[$forScope];

        return $formatter::format($this);
    }
}
