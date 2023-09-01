<?php
declare(strict_types=1);

namespace App\Models\History;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $history_id
 * @property string|null $entry_type
 * @property int|null $entry_id
 * @property string|null $entry_caption
 * @property string|null $entry_mark
 */
class HistoryLink extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property of related history record. */
    public $timestamps = false;

    /** @var string[] Fillable attributes. */
    protected $fillable = [
        'entry_type',
        'entry_id',
        'entry_caption',
        'entry_mark',
    ];

    /**
     * Format history link as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'entry_type' => $this->entry_type,
            'entry_id' => $this->entry_id,
            'entry_caption' => $this->entry_caption,
            'entry_mark' => $this->entry_mark,
        ];
    }
}
