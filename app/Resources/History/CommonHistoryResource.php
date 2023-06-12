<?php
declare(strict_types=1);

namespace App\Resources\History;

use App\Models\History\History;
use App\Resources\HistoryResource;

class CommonHistoryResource extends HistoryResource
{
    public function __construct()
    {
        $this->query = History::query()->withCount(['comments', 'links', 'changes']);
    }
}
