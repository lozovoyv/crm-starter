<?php
declare(strict_types=1);

namespace App\Utils;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModelOrder
{
    /**
     * Fix models order to properly incrementing sequence from zero.
     *
     * @param string $class
     * @param string|null $orderField
     * @param string|null $alternateOrder
     *
     * @return void
     */
    public static function fix(string $class, ?string $orderField = 'order', ?string $alternateOrder = 'id'): void
    {
        if (empty($orderField)) {
            return;
        }

        DB::transaction(static function () use ($class, $orderField, $alternateOrder) {
            /** @var Model $class */
            /** @var Collection $models */
            $models = $class::query()
                ->orderByRaw('isnull(`' . $orderField . '`) desc')
                ->orderBy($orderField)
                ->when($alternateOrder, function (Builder $query) {
                    $query->orderBy('id');
                })
                ->get();

            $lastOrder = 0;

            foreach ($models as $model) {
                /** @var Model $model */
                $model->setAttribute($orderField, $lastOrder++);
                $model->save();
            }
        });
    }
}
