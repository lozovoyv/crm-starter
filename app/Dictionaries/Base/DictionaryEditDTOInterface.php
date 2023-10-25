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

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface DictionaryEditDTOInterface
{
    /**
     * Item ID.
     *
     * @return string|int|null
     */
    public function id(): string|int|null;

    /**
     * Form title.
     *
     * @return string
     */
    public function title(): string;

    /**
     * Form values.
     *
     * @return array
     */
    public function values(): array;

    /**
     * Form validation rules.
     *
     * @return array
     */
    public function rules(): array;

    /**
     * Field titles.
     *
     * @return array
     */
    public function titles(): array;

    /**
     * Validation messages.
     *
     * @return array
     */

    public function messages(): array;

    /**
     * Item hash.
     *
     * @return string|null
     */
    public function hash(): ?string;

    /**
     * Field types.
     *
     * @return array
     */
    public function types(): array;

    /**
     * Field options.
     *
     * @return array
     */
    public function options(): array;
}
