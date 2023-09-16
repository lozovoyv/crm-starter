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
