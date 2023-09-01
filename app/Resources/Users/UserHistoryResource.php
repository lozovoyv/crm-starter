<?php
declare(strict_types=1);

namespace App\Resources\Users;

use App\Models\History\History;
use App\Models\Users\User;
use App\Resources\HistoryResource;

class UserHistoryResource extends HistoryResource
{
    public function __construct()
    {
        $this->query = History::query()
            ->withCount(['comments', 'links', 'changes'])
            ->where('entry_type', User::class);
    }
}
