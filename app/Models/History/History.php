<?php
declare(strict_types=1);

namespace App\Models\History;

use App\Builders\HistoryBuilder;
use App\Models\Positions\Position;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;

/**
 * @property int $id
 * @property int $action_id
 * @property string|null $entry_type
 * @property int|null $entry_id
 * @property string|null $entry_caption
 * @property string|null $entry_tag
 * @property string|null $description
 * @property int|null $position_id
 * @property Carbon $timestamp
 *
 * @property-read int changes_count
 * @property-read int comments_count
 *
 * @property Position|null $position
 * @property Model|null $entry
 * @property HistoryAction $action
 * @property Collection<HistoryChanges>|null $changes
 * @property Collection<HistoryLink>|null $links
 * @property Collection<HistoryComment>|null $comments
 */
class History extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property. */
    public $timestamps = false;

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    protected $fillable = [
        'action_id',
        'entry_type',
        'entry_id',
        'entry_caption',
        'entry_tag',
        'description',
        'position_id',
        'timestamp',
    ];

    protected $with = ['action', 'links', 'entry'];

    protected $withCount = ['changes', 'comments'];

    /**
     * Begin querying the model.
     *
     * @return HistoryBuilder
     */
    public static function query(): HistoryBuilder
    {
        /** @var HistoryBuilder $query */
        $query = parent::query();

        return $query;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param Builder $query
     *
     * @return HistoryBuilder
     */
    public function newEloquentBuilder($query): HistoryBuilder
    {
        return new HistoryBuilder($query);
    }

    /**
     * History record action.
     *
     * @return  HasOne
     */
    public function action(): HasOne
    {
        return $this->hasOne(HistoryAction::class, 'id', 'action_id');
    }

    /**
     * History record operator.
     *
     * @return  BelongsTo
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    /**
     * Get the related entry model.
     *
     * @return  MorphTo
     */
    public function entry(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * History record comment.
     *
     * @return  HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(HistoryComment::class, 'history_id', 'id');
    }

    /**
     * History record links.
     *
     * @return  HasMany
     */
    public function links(): HasMany
    {
        return $this->hasMany(HistoryLink::class, 'history_id', 'id');
    }

    /**
     * History record changes.
     *
     * @return  HasMany
     */
    public function changes(): HasMany
    {
        return $this->hasMany(HistoryChanges::class, 'history_id', 'id');
    }

    /**
     * Add history comment.
     *
     * @param string $comment
     *
     * @return  $this
     */
    public function addComment(string $comment): self
    {
        $this->comments()->create(['comment' => $comment]);

        return $this;
    }

    /**
     * Add history link.
     *
     * @param string|null $entryType
     * @param int|null $entryId
     * @param string|null $entryCaption
     * @param string|null $entryTag
     * @param string|null $key
     *
     * @return  $this
     *
     * @see HistoryLink
     */
    public function addLink(string $entryType = null, int $entryId = null, string $entryCaption = null, ?string $entryTag = null, ?string $key = null): self
    {
        $this->links()->create([
            'entry_type' => $entryType,
            'entry_id' => $entryId,
            'entry_caption' => $entryCaption,
            'entry_tag' => $entryTag,
            'key' => $key,
        ]);

        return $this;
    }

    /**
     * Add history changes.
     *
     * @param string|array $parameter
     * @param int|null $type
     * @param mixed|null $old
     * @param mixed|null $new
     *
     * @return  $this
     */
    public function addChanges(string|array $parameter, int $type = null, mixed $old = null, mixed $new = null): self
    {
        if (is_array($parameter)) {
            foreach ($parameter as $change) {
                if ($change instanceof HistoryChanges) {
                    $this->changes()->save($change);
                } else {
                    $this->changes()->create($parameter);
                }
            }
        } else {
            $this->changes()->create([
                'parameter' => $parameter,
                'type' => $type,
                'old' => $old,
                'new' => $new,
            ]);
        }

        return $this;
    }

    /**
     * Get formatted changes.
     *
     * @return  \Illuminate\Support\Collection
     */
    public function getChanges(): \Illuminate\Support\Collection
    {
        $this->loadMissing('changes');

        return $this->getRelation('changes')->map(function (HistoryChanges $change) {
            return $change->toArray($this->entry_type);
        });
    }

    /**
     * Format history record as array.
     *
     * @return  array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'timestamp' => $this->timestamp,
            'has_entry' => $this->entry !== null,

            'entry_type' => $this->entry_type,
            'entry_id' => $this->entry_id,
            'entry_caption' => $this->entry_caption,
            'entry_tag' => $this->entry_tag,

            'action' => $this->action->name,
            'description' => $this->description,

            'position_id' => $this->position_id,
            'position' => $this->position?->user->compactName,

            'links' => $this->links,

            'changes_count' => $this->changes_count,
            'comments_count' => $this->comments_count,
        ];
    }
}
