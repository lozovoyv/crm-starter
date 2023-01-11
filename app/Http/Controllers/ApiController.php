<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller as BaseController;

class ApiController extends BaseController
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
     * @param array $changes
     *
     * @return  void
     */
    protected function set(Model $model, string $key, mixed $value, int $type, array &$changes): void
    {
        /** @noinspection TypeUnsafeComparisonInspection */
        if ($model->getAttribute($key) != $value || !$model->exists) {
            $changes[] = ['parameter' => $key, 'type' => $type, 'old' => $model->exists ? $model->getAttribute($key) : null, 'new' => $value];
            $model->setAttribute($key, $value);
        }
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
