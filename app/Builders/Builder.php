<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Builder extends BaseBuilder
{
    /**
     * Explode search string into terms array.
     *
     * @param string|null $search
     * @param bool $useWildSearch
     *
     * @return array
     */
    protected function explodeSearchTerms(?string $search, bool $useWildSearch = false): array
    {
        if (empty($search)) {
            return [];
        }

        $terms = explode(' ', $search);

        if ($useWildSearch) {
            $terms = array_map(static function ($term) {
                return str_replace('*', '%', trim($term));
            }, $terms);
        }

        return array_values(array_filter($terms));
    }

    /**
     * Get paginated list.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return LengthAwarePaginator
     */
    public function pagination(int $page, int $perPage): LengthAwarePaginator
    {
        return $this->paginate($perPage, ['*'], null, $page);
    }
}
