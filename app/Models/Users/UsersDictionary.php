<?php

namespace App\Models\Users;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UsersDictionary extends Model
{
    /** @var string Referenced table */
    protected $table = 'users';

    public static function query(): Builder
    {
        return parent::query()
            ->select([
                'users.id',
                DB::raw('CONCAT_WS(\' \', users.lastname, users.firstname, users.patronymic) as name'),
                DB::raw('IF(users.status_id = ' . UserStatus::active . ', true, false) as enabled'),
                DB::raw('CONCAT(users.username, " — ", users.email, " — ", users.phone) as hint'),
                'users.lastname as order',
                'users.created_at as created_at',
                'users.updated_at as updated_at',
                'users.lastname',
                'users.firstname',
                'users.patronymic',
                'users.username',
                'users.email',
                'users.phone',
            ])->distinct();
    }

    public function asArray(): array
    {
        return [
            'id' => $this->getAttribute('id'),
            'name' => $this->getAttribute('name'),
            'enabled' => $this->getAttribute('enabled'),
            'order' => $this->getAttribute('order'),
            'created_at' => $this->getAttribute('created_at'),
            'updated_at' => $this->getAttribute('updated_at'),
            'lastname' => $this->getAttribute('lastname'),
            'firstname' => $this->getAttribute('firstname'),
            'patronymic' => $this->getAttribute('patronymic'),
            'username' => $this->getAttribute('username'),
            'email' => $this->getAttribute('email'),
            'phone' => $this->getAttribute('phone'),
        ];
    }
}
