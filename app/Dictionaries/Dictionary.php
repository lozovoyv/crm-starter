<?php
declare(strict_types=1);

namespace App\Dictionaries;

use App\Current;
use App\Dictionaries\Utils\EloquentDictionaryList;
use App\Dictionaries\Utils\EloquentDictionaryView;
use App\Http\Responses\ApiResponse;
use App\Models\AbstractDictionary;
use App\Models\History\History;
use App\Models\History\HistoryAction;
use App\Utils\Casting;
use App\Utils\ModelOrder;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

abstract class Dictionary implements DictionaryInterface
{
    protected static string $dictionaryClass = AbstractDictionary::class;
    protected static string $name = 'Абстрактный справочник';

    protected static bool $orderable = true;

    protected static string $id_field = 'id';
    protected static string $name_field = 'name';
    protected static ?string $hint_field = null;
    protected static ?string $enabled_field = 'enabled';
    protected static ?string $order_field = 'order';
    protected static ?string $locked_field = 'locked';
    protected static ?string $updated_at_field = 'updated_at';

    protected static array $messages = [
        'form create title' => 'Добавление записи',
        'form edit title' => 'Редактирование записи :name',
        'item created successfully' => 'Запись :name добавлена',
        'item edited successfully' => 'Запись :name сохранена',
        'item switched off' => 'Запись :name отключена',
        'item switch on' => 'Запись :name включена',
        'item deleted' => 'Запись :name удалена',
        'item not found' => 'Запись не найдена',
    ];

    protected static array $fields = [
        'name' => [
            'title' => 'Название',
            'type' => 'string',
            'column' => 'name',
            'rules' => 'required|unique',
            'messages' => ['unique' => 'Такая запись уже существует'],
            'show' => true,
        ],
    ];

    /**
     * Get dictionary items for select.
     *
     * @param bool $isEditable
     * @param string|null $ifModifiedSince
     * @param array $filters
     * @param string|null $search
     *
     * @return  DictionaryViewInterface
     */
    public static function view(bool $isEditable, ?string $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewInterface
    {
        return new EloquentDictionaryView(
            static::query(),
            static::$dictionaryClass,
            function(Model $item) { return static::asArray($item); }, // todo test this
            $isEditable,
            static::$updated_at_field,
            $ifModifiedSince,
            $filters,
            $search
        );
    }

    /**
     * Get dictionary items for editor.
     *
     * @return  DictionaryListInterface
     */
    public static function list(): DictionaryListInterface
    {
        return new EloquentDictionaryList(
            static::listQuery(),
            static::title(),
            static::getTitles(),
            static::$orderable,
            static::$enabled_field !== null,
            static::getListOptions(),
            ['static', 'asListArray'] // todo test this
        );
    }

    /**
     * Get dictionary record data for editing.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public static function get(Request $request): ApiResponse
    {
        $id = $request->input('id');

        /** @var AbstractDictionary $class */
        $class = static::$dictionaryClass;

        /** @var AbstractDictionary|Model $item */
        if ($id === null) {
            $item = new static::$dictionaryClass();
        } else {
            $item = $class::query()->where(static::$id_field, $id)->first();
            if ($item === null) {
                return ApiResponse::notFound(static::message(static::$messages['item not found']));
            }
        }

        return ApiResponse::form()
            ->title(static::message($item->exists ? static::$messages['form edit title'] : static::$messages['form create title'], $item->name))
            ->values(static::getValues($item))
            ->rules(static::getRules($item))
            ->titles(static::getTitles())
            ->messages(static::getMessages())
            ->hash(method_exists($item, 'getHash') ? $item->getHash() : null)
            ->payload([
                'types' => static::getTypes(),
                'id' => $item->id,
            ]);
    }

    /**
     * Update dictionary record.
     *
     * @param Request $request
     * @param string $alias
     *
     * @return  ApiResponse
     */
    public static function update(Request $request, string $alias): ApiResponse
    {
        /** @var AbstractDictionary|Model $class */
        $class = static::$dictionaryClass;

        $id = $request->input('id');

        /** @var AbstractDictionary|Model $item */
        if ($id === null) {
            $item = new $class();
            if (static::$orderable) {
                $item->{static::$order_field} = $class::query()->max(static::$order_field) + 1;
            }
        } else {
            $item = $class::query()->where('id', $id)->first();
            if ($item === null) {
                return ApiResponse::notFound(static::message(static::$messages['item not found']));
            }
        }

        if (method_exists($item, 'isHash') && !$item->isHash($request->input('hash'))) {
            return ApiResponse::error('Запись была изменена в другом месте.');
        }

        $data = $request->input('data');

        $validator = Validator::make($data, static::getRules($item, true), static::getMessages(), static::getTitles());

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        $changes = [];

        foreach (static::$fields as $name => $record) {
            $key = $record['column'] ?? $name;
            $changes[] = [
                'parameter' => $key,
                'type' => Casting::type($request['type'] ?? null),
                'old' => $item->getAttribute($key),
                'new' => $data[$name],
            ];
            $item->setAttribute($key, $data[$name] ?? null);
        }

        $item->save();

        $current = Current::init($request);
        $action = $item->wasRecentlyCreated ? HistoryAction::dictionary_item_created : HistoryAction::dictionary_item_edited;
        static::addHistory($alias, $item, $action, $current)->addChanges($changes);

        if (static::$orderable) {
            ModelOrder::fix(static::$dictionaryClass, static::$order_field);
        }

        return ApiResponse::success(
            static::message(
                $item->wasRecentlyCreated ? static::$messages['item created successfully'] : static::$messages['item edited successfully'],
                $item->{static::$name_field}
            )
        )
            ->payload(['id' => $item->id]);
    }

    /**
     * Delete dictionary record.
     *
     * @param Request $request
     * @param string $alias
     *
     * @return  ApiResponse
     */
    public static function delete(Request $request, string $alias): ApiResponse
    {
        $id = $request->input('id');

        /** @var AbstractDictionary|Model $class */
        $class = static::$dictionaryClass;

        /** @var AbstractDictionary|Model $item */
        $item = $class::query()->where('id', $id)->first();
        if ($item === null) {
            return ApiResponse::notFound('Запись не найдена');
        }

        if (method_exists($item, 'isHash') && !$item->isHash($request->input('hash'))) {
            return ApiResponse::error('Запись была изменена в другом месте.');
        }

        $current = Current::init($request);
        $history = static::addHistory($alias, $item, HistoryAction::dictionary_item_deleted, $current);

        foreach (static::$fields as $name => $record) {
            $old = isset($record['column']) ? $item->{$record['column']} : $item->{$name};
            $history->addChanges($request['column'] ?? $name, $record['type'] ?? null, $old);
        }

        $item->delete();

        ModelOrder::fix(static::$dictionaryClass, static::$order_field);

        return ApiResponse::success(static::message(static::$messages['item deleted'], $item->{static::$name_field}))
            ->payload(['id' => $item->id]);
    }

    /**
     * Toggle dictionary entry state.
     *
     * @param Request $request
     * @param string $alias
     *
     * @return  ApiResponse
     */
    public static function toggle(Request $request, string $alias): ApiResponse
    {
        $id = $request->input('id');

        /** @var AbstractDictionary|Model $class */
        $class = static::$dictionaryClass;

        /** @var AbstractDictionary|Model $item */
        $item = $class::query()
            ->where(static::$id_field, $id)
            ->select([
                'id',
                static::$name_field,
                static::$enabled_field,
                static::$updated_at_field,
            ])->first();

        if ($item === null) {
            return ApiResponse::notFound(static::message(static::$messages['item not found']));
        }

        if (!$item->isHash($request->input('hash'))) {
            return ApiResponse::error('Запись была изменена в другом месте.');
        }

        $item->setAttribute(static::$enabled_field, $request->input('state', false));
        $item->save();

        $current = Current::init($request);
        $action = $item->{static::$enabled_field} ? HistoryAction::dictionary_item_activated : HistoryAction::dictionary_item_deactivated;
        static::addHistory($alias, $item, $action, $current);

        return ApiResponse::success(
            static::message(
                $item->{static::$enabled_field} ? static::$messages['item switch on'] : static::$messages['item switched off'],
                $item->{static::$name_field}
            )
        )
            ->payload([
                'enabled' => $item->{static::$enabled_field},
                'hash' => $item->getHash(),
                'id' => $item->id,
            ]);
    }

    /**
     * Sync dictionary order and enabled status.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public static function sync(Request $request): ApiResponse
    {
        $data = $request->input('data', []);

        // validate data
        foreach ($data as $item) {
            if (!array_key_exists('id', $item) && !is_int($item['id'])) {
                return ApiResponse::error('Ошибка. Неверно задан ID элемента');
            }
        }

        // sort items, remake order from 0 and remove gaps
        usort($data, static function (array $itemA, array $itemB) {
            $hasA = array_key_exists('order', $itemA);
            $hasB = array_key_exists('order', $itemB);
            if (!$hasA && !$hasB) {
                return 0;
            }
            if (!$hasA) {
                return -1;
            }
            if (!$hasB) {
                return 1;
            }

            return $itemA['order'] <=> $itemB['order'];
        });

        $lastOrder = 0;

        foreach ($data as &$item) {
            $item['order'] = $lastOrder++;
        }
        unset($item);

        $update = [];
        $updatedIds = [];

        foreach ($data as $item) {
            $update[$item['id']] = $item['order'];
            $updatedIds[] = $item['id'];
        }

        try {
            DB::transaction(static function () use ($update, $updatedIds, &$lastOrder) {
                /** @var AbstractDictionary $class */
                $class = static::$dictionaryClass;

                $processing = $class::query()->whereIn('id', $updatedIds)->get();
                foreach ($processing as $item) {
                    /** @var AbstractDictionary $item */
                    $item->setAttribute(static::$order_field, $update[$item->id]);
                    $item->save();
                }
                $unprocessed = $class::query()->whereNotIn('id', $updatedIds)->orderBy('order')->get();
                foreach ($unprocessed as $item) {
                    /** @var AbstractDictionary $item */
                    $item->setAttribute(static::$order_field, $lastOrder++);
                    $item->save();
                }
            });
        } catch (Exception $exception) {

            return ApiResponse::error("Ошибка обновления справочника " . static::$name)->payload(['exception' => $exception->getMessage()]);
        }

        return APIResponse::success("Справочник " . static::$name . " обновлён");
    }

    /**
     * Get dictionary title.
     *
     * @return string
     */
    public static function title(): string
    {
        return static::$name;
    }

    /**
     * The query for dictionary view.
     *
     * @return  Builder
     */
    protected static function query(): Builder
    {
        /** @var AbstractDictionary $class */
        $class = static::$dictionaryClass;

        $fields = array_filter([
            static::$id_field . ' as id',
            static::$name_field . ' as name',
            static::$hint_field ? static::$hint_field . ' as hint' : null,
            static::$enabled_field ? static::$enabled_field . ' as enabled' : null,
            static::$order_field ? static::$order_field . ' as order' : null,
            static::$updated_at_field ? static::$updated_at_field . ' as updated_at' : null,
        ]);

        return $class::query()
            ->select($fields)
            ->orderBy(static::$order_field ?? static::$name_field);
    }

    /**
     * The query for dictionary view.
     *
     * @return  Builder
     */
    protected static function listQuery(): Builder
    {
        /** @var AbstractDictionary $class */
        $class = static::$dictionaryClass;

        $fields = [
            static::$id_field . ' as id',
            static::$enabled_field ? static::$enabled_field . ' as enabled' : null,
            static::$order_field ? static::$order_field . ' as order' : null,
            static::$locked_field ? static::$locked_field . ' as locked' : null,
            static::$updated_at_field ? static::$updated_at_field . ' as updated_at' : null,
        ];

        foreach (static::$fields as $name => $field) {
            if (array_key_exists('show', $field) && $field['show']) {
                $fields[] = ($field['column'] ?? $name) . " as $name";
            }
        }

        return $class::query()
            ->select(array_filter($fields))
            ->orderBy(static::$order_field ?? static::$name_field);
    }

    /**
     * Format output record.
     *
     * @param Model $model
     *
     * @return  array
     */
    protected static function asArray(Model $model): array
    {
        return $model->toArray();
    }

    /**
     * Format output record for editor list.
     *
     * @param Model $model
     *
     * @return  array
     */
    protected static function asListArray(Model $model): array
    {
        return $model->toArray();
    }

    /**
     * Get types array for item editing form.
     *
     * @return array
     */
    protected static function getListOptions(): array
    {
        return array_filter(
            array_map(static function (array $record) {
                if (!isset($record['show'])) {
                    return null;
                }

                return [
                    'type' => $record['type'],
                ];
            }, static::$fields)
        );
    }

    /**
     * Get values array for item editing form.
     *
     * @param AbstractDictionary|Model $item
     *
     * @return array
     */
    protected static function getValues(AbstractDictionary|Model $item): array
    {
        $values = [];

        foreach (static::$fields as $key => $record) {
            $values[$key] = $item->getAttribute($record['column'] ?? $key);
        }

        return $values;
    }

    /**
     * Get types array for item editing form.
     *
     * @return array
     */
    protected static function getTypes(): array
    {
        return array_map(static function (array $record) {
            return $record['type'];
        }, static::$fields);
    }

    /**
     * Get rules array for item validation.
     *
     * @param AbstractDictionary|Model $item
     * @param bool $withDatabaseRules
     *
     * @return array
     */
    protected static function getRules(AbstractDictionary|Model $item, bool $withDatabaseRules = false): array
    {
        $rules = [];

        foreach (static::$fields as $key => $record) {
            if (array_key_exists('rules', $record)) {
                if (!$withDatabaseRules) {
                    $rules[$key] = $record['rules'];
                } else {
                    $ruleSet = explode('|', $record['rules']);
                    foreach ($ruleSet as $rule) {
                        if ($rule === 'unique') {
                            $rules[$key][] = Rule::unique($item->getTable())->ignore($item->id);
                        } else {
                            $rules[$key][] = $rule;
                        }
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Get titles array for item edit form.
     *
     * @return array
     */
    protected static function getTitles(): array
    {
        return array_map(static function (array $record) {
            return $record['title'];
        }, static::$fields);
    }

    /**
     * Get messages array for item validation.
     *
     * @return array
     */
    protected static function getMessages(): array
    {
        $messages = [];

        foreach (static::$fields as $name => $record) {
            if (array_key_exists('messages', $record)) {
                foreach ($record['messages'] as $key => $message) {
                    $messages["$name.$key"] = $message;
                }
            }
        }

        return $messages;
    }

    /**
     * Get formatted message.
     *
     * @param string $message
     * @param string|null $name
     *
     * @return string
     */
    protected static function message(string $message, ?string $name = null): string
    {
        return str_replace([':name', '  '], [$name ?? '', ' '], $message);
    }

    /**
     * Add history record for dictionary entry.
     *
     * @param string $alias
     * @param AbstractDictionary|Model $item
     * @param int $action_id
     * @param Current $current
     *
     * @return History
     */
    protected static function addHistory(string $alias, AbstractDictionary|Model $item, int $action_id, Current $current): History
    {
        $history = new History([
            'action_id' => $action_id,
            'entry_title' => static::title() . ' "' . $item->{static::$name_field} . '"',
            'entry_name' => get_class($item),
            'entry_type' => 'dictionary_' . $alias,
            'entry_id' => $item->{static::$id_field},
            'position_id' => $current->positionId(),
            'timestamp' => Carbon::now(),
        ]);

        $history->save();

        return $history;
    }
}
