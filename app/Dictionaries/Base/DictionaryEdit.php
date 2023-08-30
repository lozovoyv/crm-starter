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

namespace App\Dictionaries\Base;

class DictionaryEdit implements DictionaryEditInterface
{
    protected string|int|null $id;
    protected string $title;
    protected array $values;
    protected array $rules;
    protected array $titles;
    protected array $messages;
    protected ?string $hash;
    protected array $types;
    protected array $options;

    public function __construct(string|int|null $id, string $title, array $values, array $rules, array $titles, array $messages, ?string $hash, array $types, array $options)
    {
        $this->id = $id;
        $this->title = $title;
        $this->values = $values;
        $this->rules = $rules;
        $this->titles = $titles;
        $this->messages = $messages;
        $this->hash = $hash;
        $this->types = $types;
        $this->options = $options;
    }

    /**
     * Item ID.
     *
     * @return string|int|null
     */
    public function id(): string|int|null
    {
        return $this->id;
    }

    /**
     * Form title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Form values.
     *
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * Form validation rules.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->rules;
    }

    /**
     * Field titles.
     *
     * @return array
     */
    public function titles(): array
    {
        return $this->titles;
    }

    /**
     * Validation messages.
     *
     * @return array
     */

    public function messages(): array
    {
        return $this->messages;
    }

    /**
     * Item hash.
     *
     * @return string|null
     */
    public function hash(): ?string
    {
        return $this->hash;
    }

    /**
     * Field types.
     *
     * @return array
     */
    public function types(): array
    {
        return $this->types;
    }

    /**
     * Field options.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }
}
