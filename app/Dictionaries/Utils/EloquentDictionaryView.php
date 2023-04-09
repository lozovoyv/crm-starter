<?php
declare(strict_types=1);

namespace App\Dictionaries\Utils;

use App\Dictionaries\DictionaryViewInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentDictionaryView implements DictionaryViewInterface
{
    /** @var Collection|array|null Dictionary items. */
    protected Collection|array|null $items;

    /** @var Carbon|null Last modification date. */
    protected ?Carbon $lastModifiedAt;

    /** @var bool Whether dictionary modified since given timestamp. */
    protected bool $notModified = false;

    /**
     * Items getter.
     *
     * @return Collection|array|null
     */
    public function items(): Collection|array|null
    {
        return $this->items ?? null;
    }

    /**
     * Last modification time getter.
     *
     * @return Carbon|null
     */
    public function lastModified(): ?Carbon
    {
        return $this->lastModifiedAt ?? null;
    }

    /**
     * Not modified flag getter.
     *
     * @return bool
     */
    public function isNotModified(): bool
    {
        return $this->notModified;
    }

    /**
     * @param Builder $query
     * @param string $class
     * @param callable|null $transform
     * @param bool $withHash
     * @param string|null $updatedAtField
     * @param string|null $ifModifiedSince
     * @param array $filters
     * @param string|null $search
     */
    public function __construct(
        Builder $query,
        string $class,
        ?callable $transform = null,
        bool $withHash = false,
        ?string $updatedAtField = 'updated_at',
        ?string $ifModifiedSince = null,
        array $filters = [],
        ?string $search = null
    )
    {
        if (!empty($filters) && method_exists($class, 'filter')) {
            $query = $class::filter($query, $filters);
        }

        if ($search && method_exists($class, 'search')) {
            $query = $class::search($query, $search);
        }

        if ($updatedAtField) {
            $actual = $query->clone()->latest($updatedAtField)->value($updatedAtField);
            $actual = Carbon::parse($actual)->setTimezone('GMT');

            if ($ifModifiedSince) {
                $requested = Carbon::createFromFormat('D\, d M Y H:i:s \G\M\T', $ifModifiedSince, 'GMT');
                if ($requested >= $actual) {
                    $this->items = null;
                    $this->lastModifiedAt = $actual;
                    $this->notModified = true;
                }
            }
        }

        $items = $query->get();

        $items->transform(function (Model $item) use ($transform, $withHash) {
            $result = !is_null($transform) ? $transform($item) : $item->toArray();
            if ($withHash && method_exists($item, 'getHash')) {
                $result['hash'] = $item->getHash();
            }
            return $result;
        });

        $this->items = $items;
    }
}
