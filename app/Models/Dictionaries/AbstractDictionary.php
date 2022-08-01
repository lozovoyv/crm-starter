<?php

namespace App\Models\Dictionaries;

use App\Models\Model;
use Carbon\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property bool $locked
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
abstract class AbstractDictionary extends Model
{
    /** @var array Attributes casting. */
    protected $casts = [
        'enabled' => 'boolean',
        'locked' => 'boolean',
        'order' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get dictionary item instance by an ID.
     *
     * @param int $id
     *
     * @return  Model|null
     */
    public static function get(int $id): ?AbstractDictionary
    {
        /** @var Model $model */
        $model = self::query()->where('id', $id)->first();

        return $model ?? null;
    }
}
