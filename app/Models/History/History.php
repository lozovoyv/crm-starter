<?php

namespace App\Models\History;

use App\Models\Positions\Position;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $action_id
 * @property int|null $history_line_id
 * @property string $entry_name
 * @property int|null $entry_id
 * @property string|null $description
 * @property int|null $position_id
 * @property Carbon $timestamp
 *
 * @property Position $position
 * @property HistoryAction $action
 * @property Collection|null $comments
 * @property Collection|null $links
 * @property Collection|null $changes
 */
class History extends Model
{
    /** @var bool No need timestamps here. Record created once, time is stored in timestamp property. */
    public $timestamps = false;

    /** @var array Attribute casting */
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    /** @var array Fillable attributes */
    protected $fillable = [
        'action_id',
        'history_line_id',
        'entry_name',
        'entry_id',
        'description',
        'position_id',
        'timestamp',
    ];

    /** @var string[] Eager load relation */
    protected $with = ['action', 'comments', 'links', 'changes', 'position'];

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
     * @param string $entryTitle
     * @param string|null $entryName
     * @param int|null $entryId
     *
     * @return  $this
     */
    public function addLink(string $entryTitle, ?string $entryName = null, ?int $entryId = null): self
    {
        $this->links()->create(['entry_title' => $entryTitle, 'entry_name' => $entryName, 'entry_id' => $entryId]);

        return $this;
    }

    /**
     * Add history changes.
     *
     * @param string $parameter
     * @param int $type
     * @param $old
     * @param $new
     *
     * @return  $this
     */
    public function addChanges(string $parameter, int $type, $old, $new): self
    {
        $this->changes()->create([
            'parameter' => $parameter,
            'type' => $type,
            'old' => $old,
            'new' => $new,
        ]);

        return $this;
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
            'entry_name' => $this->entry_name,
            'entry_id' => $this->entry_id,

            'action' => $this->action->name,
            'description' => $this->description,

            'position_id' => $this->position_id,
            'position' => $this->position?->user->info->compactName,

            'links' => $this->links,
            'changes' => $this->changes,
            'comments' => $this->comments,
        ];
    }
}
