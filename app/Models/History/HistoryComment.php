<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $history_id
 * @property string $comment
 */
class HistoryComment extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property of related history record. */
    public $timestamps = false;

    /** @var string[] Fillable attributes. */
    protected $fillable = ['comment'];

    /**
     * Cast to array.
     *
     * @return string[]
     */
    public function toArray(): array
    {
        return ['comment' => $this->comment];
    }
}
