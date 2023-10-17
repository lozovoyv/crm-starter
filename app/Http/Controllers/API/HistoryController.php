<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;

abstract class HistoryController extends ApiController
{
    protected array $titles = [
        'timestamp' => 'history/history.timestamp',
        'action' => 'history/history.action',
        'links' => 'history/history.links',
        'comment' => 'history/history.comment',
        'changes' => 'history/history.changes',
        'position_id' => 'history/history.position',
    ];

    protected array $orderableColumns = ['timestamp'];

    protected array $changesTitles = [
        'parameter' => 'history/history.parameter',
        'old_value' => 'history/history.old_value',
        'new_value' => 'history/history.new_value',
    ];
}
