<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\History\HistoryChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /**
     * Get data from request.
     *
     * @param Request $request
     *
     * @return  array
     */
    protected function data(Request $request): array
    {
        return $request->input('data', []);
    }

    /**
     * Validate data and return validation errors.
     *
     * @param array $data
     * @param array $rules
     * @param array $titles
     * @param array $messages
     *
     * @return  array|null
     */
    protected function validate(array $data, array $rules, array $titles, array $messages = []): ?array
    {
        $validator = Validator::make(
            $data, $rules, $messages, array_map(static function ($title) {
                return '"' . mb_strtolower($title) . '"';
            }, $titles)
        );

        return $validator->fails() ? $validator->getMessageBag()->toArray() : null;
    }

    /**
     * Set attribute and trace changes.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param int $type
     *
     * @return HistoryChanges|null
     */
    protected function set(Model $model, string $key, mixed $value, int $type): ?HistoryChanges
    {
        /** @noinspection TypeUnsafeComparisonInspection */
        if (!$model->exists || $model->getAttribute($key) != $value) {

            $changes = new HistoryChanges([
                'parameter' => $key,
                'type' => $type,
                'old' => $model->exists ? $model->getAttribute($key) : null,
                'new' => $value,
            ]);

            $model->setAttribute($key, $value);

            return $changes;
        }

        return null;
    }

    /**
     * Retrieve model by id or create new.
     *
     * @param string $class
     * @param int|null $id
     * @param array $with
     * @param array $withCount
     * @param array $where
     *
     * @return  \App\Models\Model|null
     */
    protected function firstOrNew(string $class, ?int $id, array $where = [], array $with = [], array $withCount = []): ?Model
    {
        /** @var Model $class */

        if ($id === null) {
            return null;
        }

        if ($id === 0) {
            return new $class();
        }

        return $this->first($class, $id, $where, $with, $withCount);
    }

    /**
     * Retrieve model by id.
     *
     * @param string $class
     * @param int|null $id
     * @param array $where
     *
     * @param array $with
     * @param array $withCount
     *
     * @return  Model|null
     */
    protected function first(string $class, ?int $id, array $where = [], array $with = [], array $withCount = []): ?Model
    {
        /** @var Model $class */

        if ($id === null) {
            return null;
        }

        /** @var Model $model */
        $model = $class::query()->where('id', $id)->where($where)->with($with)->withCount($withCount)->first();

        return $model ?? null;
    }
}
