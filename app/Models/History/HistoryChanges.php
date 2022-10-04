<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Model;

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
}
