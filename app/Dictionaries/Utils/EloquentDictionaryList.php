<?php
declare(strict_types=1);

namespace App\Dictionaries\Utils;

use App\Dictionaries\DictionaryListInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentDictionaryList implements DictionaryListInterface
{
    /** @var Collection|array|null Dictionary items. */
    protected Collection|array|null $items;

    protected string $title;
    protected array $titles;
    protected bool $orderable;
    protected bool $switchable;
    protected array $fields;

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
     * Dictionary title.
     *
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Dictionary item props titles.
     *
     * @return array
     */
    public function titles(): array
    {
        return $this->titles;
    }

    /**
     * Whether is dictionary sortable.
     *
     * @return bool
     */
    public function orderable(): bool
    {
        return $this->orderable;
    }

    /**
     * Whether is dictionary items switchable.
     *
     * @return bool
     */
    public function switchable(): bool
    {
        return $this->switchable;
    }

    /**
     * Dictionary items fields details.
     *
     * @return array
     */
    public function fields(): array
    {
        return $this->fields;
    }

    /**
     * @param Builder $query
     * @param string $title
     * @param array $titles
     * @param bool $orderable
     * @param bool $switchable
     * @param array $fields
     * @param callable|null $transform
     */
    public function __construct(
        Builder $query,
        string $title,
        array $titles,
        bool $orderable,
        bool $switchable,
        array $fields,
        ?callable $transform = null,
    )
    {
        $items = $query->get();

        $items->transform(function (Model $item) use ($transform) {
            $result = !is_null($transform) ? $transform($item) : $item->toArray();
            $result['hash'] = method_exists($item, 'getHash') ? $item->getHash() : null;

            return $result;
        });

        $this->items = $items;
        $this->title = $title;
        $this->titles = $titles;
        $this->orderable = $orderable;
        $this->switchable = $switchable;
        $this->fields = $fields;
    }
}
