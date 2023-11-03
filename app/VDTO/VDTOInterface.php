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

interface VDTOInterface
{
    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules(): array;

    /**
     * Get fields titles.
     *
     * @return array
     */
    public function getTitles(): array;

    /**
     * Get custom validation messages.
     *
     * @return array
     */
    public function getValidationMessages(): array;

    /**
     * Validate data and return validation errors.
     *
     * @param array $only
     *
     * @return  array|null
     */
    public function validate(array $only = []): ?array;
}
