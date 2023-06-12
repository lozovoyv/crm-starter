<?php
declare(strict_types=1);

namespace App\Resources;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

abstract class EntryResource
{
    protected array $rules = [];
    protected array $titles = [];
    protected array $messages = [];

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->rules;
    }

    /**
     * Get fields titles.
     *
     * @return array
     */
    public function getTitles(): array
    {
        return $this->titles;
    }

    /**
     * Get custom validation
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        return $this->messages;
    }

    /**
     * Filter data.
     *
     * @param array $data
     * @param array $only
     *
     * @return  array
     */
    public function filterData(array $data, array $only = []): array
    {
        return !empty($only) ? Arr::only($data, $only) : $data;
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
    protected function validateData(array $data, array $rules = [], array $titles = [], array $messages = []): ?array
    {
        $validator = Validator::make(
            $data,
            $rules,
            $messages,
            array_map(static function ($title) {
                return '"' . mb_strtolower($title) . '"';
            }, $titles)
        );

        return $validator->fails() ? $validator->getMessageBag()->toArray() : null;
    }
}
