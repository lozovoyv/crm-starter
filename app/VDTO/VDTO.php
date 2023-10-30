<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\VDTO;

use App\Utils\Translate;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

abstract class VDTO
{
    protected array $rules = [];
    protected array $titles = [];
    protected array $messages = [];

    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * Get an attribute from the VDTO.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Set an attribute to the VDTO.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get validation rules.
     *
     * @param array|null $only
     *
     * @return array
     */
    public function getValidationRules(?array $only = null): array
    {
        return $this->filter($this->rules, $only);
    }

    /**
     * Get fields titles.
     *
     * @param array|null $only
     *
     * @return array
     */
    public function getTitles(?array $only = null): array
    {
        return Translate::array($this->filter($this->titles, $only));
    }

    /**
     * Get custom validation messages.
     *
     * @param array|null $only
     *
     * @return array
     */
    public function getValidationMessages(?array $only = null): array
    {
        return Translate::array(Arr::dot($this->filter(Arr::undot($this->messages), $only)));
    }

    /**
     * Validate attributes and return validation errors.
     *
     * @param array $only
     *
     * @return array|null
     */
    public function validate(array $only = []): ?array
    {
        $rules = $this->rules;

        if (!empty($only)) {
            $rules = Arr::only($rules, $only);
        }

        return $this->validateAttributes($this->attributes, $rules, $this->titles, $this->messages);
    }

    /**
     * Conditionally filter data array.
     *
     * @param array $data
     * @param array|null $only
     *
     * @return array
     */
    protected function filter(array $data, ?array $only): array
    {
        if ($only !== null) {
            $data = Arr::only($data, $only);
        }

        return $data;
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
    protected function validateAttributes(array $data, array $rules = [], array $titles = [], array $messages = []): ?array
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

    /**
     * Dynamically retrieve attributes on the VDTO.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the VDTO.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Check whether an attribute is on the VDTO.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Unset an attribute is on the VDTO.
     *
     * @param string $key
     *
     * @return void
     */
    public function __unset(string $key): void
    {
        unset($this->attributes[$key]);
    }
}
