<?php
declare(strict_types=1);

namespace App\Resources;

abstract class ListSearchableResource extends ListResource
{
    protected ?string $search;

    /**
     * Remember applied filters.
     *
     * @param string|null $search
     *
     * @return $this
     */
    public function search(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get applied search.
     *
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * Explode search string to array.
     *
     * @param string|null $search
     *
     * @return  array
     */
    protected function explodeSearch(?string $search): array
    {
        if (empty($search)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map(static function ($term) {
                    return str_replace('*', '%', trim($term));
                }, explode(' ', $search))
            )
        );
    }
}
