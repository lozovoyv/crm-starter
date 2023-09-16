<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Models\History;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $history_id
 * @property string|null $entry_type
 * @property int|null $entry_id
 * @property string|null $entry_caption
 * @property string|null $entry_tag
 * @property string|null $key
 * @property Model|null $entry
 */
class HistoryLink extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property of related history record. */
    public $timestamps = false;

    protected $fillable = [
        'entry_type',
        'entry_id',
        'entry_caption',
        'entry_tag',
        'has_entry',
        'key',
    ];

    protected $with = ['entry'];

    /**
     * Get the related entry model.
     *
     * @return  MorphTo
     */
    public function entry(): MorphTo
    {
        return $this->morphTo();
    }

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
            'entry_tag' => $this->entry_tag,
            'has_entry' => $this->entry !== null,
            'key' => $this->key,
        ];
    }
}
