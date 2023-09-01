<?php
declare(strict_types=1);

namespace App\Models\History;

use App\Exceptions\CastingException;
use App\HistoryFormatters\FormatterInterface;
use App\Utils\Casting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

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

    /**
     * Convert old value from store value to real type.
     *
     * @param string|null $value
     *
     * @return  string|array|bool|int|Carbon|null
     */
    public function getOldAttribute(?string $value): string|array|bool|int|null|Carbon
    {
        try {
            return $value !== null ? Casting::fromString($value, $this->type) : null;
        } catch (CastingException) {
            return null;
        }
    }

    /**
     * Convert old value to store value.
     *
     * @param string|array|bool|int|Carbon|null $value
     *
     * @return  void
     */
    public function setOldAttribute(string|array|bool|int|null|Carbon $value): void
    {
        try {
            $this->attributes['old'] = $value !== null ? Casting::toString($value, $this->type) : null;
        } catch (CastingException) {
            return;
        }
    }

    /**
     * Convert new value from store value to real type.
     *
     * @param string|null $value
     *
     * @return  string|array|bool|int|Carbon|null
     */
    public function getNewAttribute(?string $value): string|array|bool|int|null|Carbon
    {
        try {
            return $value !== null ? Casting::fromString($value, $this->type) : null;
        } catch (CastingException) {
            return null;
        }
    }

    /**
     * Convert new value to store value.
     *
     * @param string|array|bool|int|Carbon|null $value
     *
     * @return  void
     */
    public function setNewAttribute(string|array|bool|int|null|Carbon $value): void
    {
        try {
            $this->attributes['new'] = $value !== null ? Casting::toString($value, $this->type) : null;
        } catch (CastingException) {
            return;
        }
    }

    /**
     * Cast to array using formatter.
     *
     * @param string|null $entryType
     *
     * @return array
     */
    public function toArray(?string $entryType = null): array
    {
        /** @var FormatterInterface|null $formatter */
        $formatter = Config::get("history.formatters.$entryType");

        if ($entryType === null || $formatter === null) {
            return [
                'parameter' => $this->parameter,
                'old' => $this->old,
                'new' => $this->new,
            ];
        }

        return $formatter::format($this);
    }
}
