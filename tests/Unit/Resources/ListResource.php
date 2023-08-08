<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use App\Models\Users\User;
use App\Resources\ListResource as BaseListResource;

class ListResource extends BaseListResource
{
    public function __construct()
    {
        $this->query = User::query();
    }
}
