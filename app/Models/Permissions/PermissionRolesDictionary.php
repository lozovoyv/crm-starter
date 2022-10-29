<?php

namespace App\Models\Permissions;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property bool $active
 * @property bool $locked
 * @property Collection $permissions
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PermissionRolesDictionary extends Model
{
    protected $table = 'permission_roles';

    public static function query(): Builder
    {
        return parent::query()
            ->select([
                'id',
                'name',
                'active as enabled',
                'description as hint',
                'name as order',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * Cast as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'name' => $this->getAttribute('name'),
            'enabled' => $this->getAttribute('enabled'),
            'hint' => $this->getAttribute('hint'),
            'order' => $this->getAttribute('order'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
        ];
    }
}
