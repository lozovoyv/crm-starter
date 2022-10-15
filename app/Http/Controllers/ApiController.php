<?php

namespace App\Http\Controllers;

use App\Models\Model;
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
        $validator = Validator::make($data, $rules, $messages, array_map(static function ($title) {
            return '"' . mb_strtolower($title) . '"';
        }, $titles));

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
        if ($model->getAttribute($key) != $value) {
            $changes[] = ['parameter' => $key, 'type' => $type, 'old' => $model->getAttribute($key), 'new' => $value];
            $model->setAttribute($key, $value);
        }
    }
}
