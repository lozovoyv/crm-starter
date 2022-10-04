<?php

namespace App\Models\History;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $history_id
 * @property string $entry_name
 * @property int|null $entry_id
 */
class HistoryLink extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property of related history record. */
    public $timestamps = false;

    /** @var string[] Fillable attributes. */
    protected $fillable = ['entry_name', 'entry_id'];
}
