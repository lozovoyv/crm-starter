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

use App\Current;
use App\Exceptions\Dictionary\DictionaryForbiddenException;
use App\Http\Responses\ApiResponse;
use Carbon\Carbon;

interface DictionaryInterface
{
    /**
     * Get dictionary items for select.
     *
     * @param Current $current
     * @param Carbon|null $ifModifiedSince
     * @param array $filters
     * @param string|null $search
     *
     * @return DictionaryViewDTOInterface
     * @throws DictionaryForbiddenException
     */
    public static function view(Current $current, ?Carbon $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewDTOInterface;

    /**
     * Get dictionary items for editor.
     *
     * @param Current $current
     *
     * @return DictionaryListDTOInterface
     * @throws DictionaryForbiddenException
     */
    public static function list(Current $current): DictionaryListDTOInterface;

    /**
     * Get dictionary record data for editing form.
     *
     * @param int|string|null $id
     * @param Current $current
     *
     * @return  DictionaryEditDTOInterface
     */
    public static function get(int|string|null $id, Current $current): DictionaryEditDTOInterface;
}
