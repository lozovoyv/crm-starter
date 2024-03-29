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
use App\Exceptions\Dictionary\DictionaryNotFoundException;
use App\Http\Responses\ApiResponse;
use App\Models\History\HistoryAction;
use App\Utils\Casting;
use App\Utils\ModelOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

abstract class EloquentDictionary extends Dictionary implements DictionaryInterface
{
    protected static string $dictionaryModel = Model::class;

    protected static bool $orderable = true;

    protected static string $id_field = 'id';
    protected static string $name_field = 'name';
    protected static ?string $hint_field = null;
    protected static ?string $enabled_field = 'enabled';
    protected static ?string $order_field = 'order';
    protected static ?string $locked_field = 'locked';
    protected static ?string $updated_at_field = 'updated_at';

    protected static array $fields = [
        'name' => [ // key name
            'title' => 'Name', // column and field title (direct or localization key)
            'type' => 'string', // type of field
            'column' => 'name', // table column name
            'validation_rules' => 'required|unique',
            'validation_messages' => [
                'required' => 'validation.required',
                'unique' => 'validation.unique',
            ],
            'options' => [], // additional options for edit form
            'show' => true, // show column in editor list
            'edit' => true, // show field in edit form
        ],
    ];

    /**
     * The query for dictionary view.
     *
     * @return  Builder
     */
    public static function query(): Builder
    {
        /** @var Model $class */
        $class = static::$dictionaryModel;

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
     * Dictionary filtering boilerplate.
     *
     * @param Builder $query
     * @param array $filters
     *
     * @return Builder
     */
    public static function filter(Builder $query, array $filters = []): Builder
    {
        return $query;
    }

    /**
     * Dictionary search boilerplate.
     *
     * @param Builder $query
     * @param string|null $search
     *
     * @return Builder
     */
    public static function search(Builder $query, ?string $search = null): Builder
    {
        return $query;
    }

    /**
     * Format output record.
     *
     * @param Model $model
     *
     * @return array
     */
    public static function asArray(Model $model): array
    {
        return $model->toArray();
    }

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
    public static function view(Current $current, ?Carbon $ifModifiedSince = null, array $filters = [], ?string $search = null): DictionaryViewDTOInterface
    {
        if (!static::canView($current)) {
            throw new DictionaryForbiddenException(static::messageDictionaryForbidden(static::title()));
        }

        $query = static::query();
        $query = static::filter($query, $filters);
        $query = static::search($query, $search);

        if (static::$updated_at_field) {
            $actual = $query->clone()->latest(static::$updated_at_field)->value(static::$updated_at_field);
            $actual = Carbon::parse($actual)->setTimezone('GMT');

            if ($ifModifiedSince && $ifModifiedSince >= $actual) {
                return new DictionaryViewDTO(null, $actual, true);
            }
        }

        $isEditable = static::canEdit($current);

        $items = $query->get();

        $items->transform(function (Model $item) use ($isEditable) {
            $result = static::asArray($item);
            if ($isEditable && method_exists($item, 'getHash')) {
                $result['hash'] = $item->getHash();
            }
            return $result;
        });

        return new DictionaryViewDTO($items, $actual ?? null, false, $isEditable);
    }

    /**
     * The list query for dictionary editor.
     *
     * @return  Builder
     */
    public static function listQuery(): Builder
    {
        /** @var Model $class */
        $class = static::$dictionaryModel;

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
     * Get dictionary items for editor.
     *
     * @param Current $current
     *
     * @return DictionaryListDTOInterface
     * @throws DictionaryForbiddenException
     */
    public static function list(Current $current): DictionaryListDTOInterface
    {
        if (!static::canEdit($current)) {
            throw new DictionaryForbiddenException(static::messageDictionaryForbidden(static::title()));
        }

        $items = static::listQuery()->get();

        $items->transform(function (Model $item) {
            $result = static::asListArray($item);
            $result['hash'] = method_exists($item, 'getHash') ? $item->getHash() : null;

            return $result;
        });

        return new DictionaryListDTO(
            $items,
            static::title(),
            static::fieldTitles(true, false),
            static::$orderable,
            static::$enabled_field !== null,
            static::fieldTypes(true, false),
        );
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
     * Get dictionary record data for editing form.
     *
     * @param int|string|null $id
     * @param Current $current
     *
     * @return DictionaryEditDTOInterface
     * @throws DictionaryNotFoundException
     * @throws DictionaryForbiddenException
     */
    public static function get(int|string|null $id, Current $current): DictionaryEditDTOInterface
    {
        if (!static::canEdit($current)) {
            throw new DictionaryForbiddenException(static::messageDictionaryForbidden(static::title()));
        }

        /** @var Model $class */
        $class = static::$dictionaryModel;

        /** @var Model $item */
        if ($id === null) {
            $item = new $class();
        } else {
            $item = $class::query()->where(static::$id_field, $id)->first();
            if ($item === null) {
                throw new DictionaryNotFoundException(static::messageItemNotFound());
            }
        }

        return new DictionaryEditDTODTO(
            $item->{static::$id_field},
            $item->exists ? static::titleFormEdit($item->{static::$name_field}) : static::titleFormCreate(),
            static::getValues($item),
            static::fieldRules(),
            static::fieldTitles(false, true),
            static::fieldMessages(),
            method_exists($item, 'getHash') ? $item->getHash() : null,
            static::fieldTypes(false, true),
            static::fieldOptions(),
        );
    }

    /**
     * Get item values array for editing form.
     *
     * @param Model $item
     *
     * @return array
     */
    protected static function getValues(Model $item): array
    {
        $values = [];

        foreach (static::$fields as $key => $record) {
            if(isset($record['edit']) && $record['edit']) {
                $values[$key] = $item->getAttribute($record['column'] ?? $key);
            }
        }

        return $values;
    }

    /**
     * TODO refactor:
     */

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
        $class = static::$dictionaryModel;

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
                return ApiResponse::notFound(static::message(static::$localizations['item not found']));
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
            ModelOrder::fix(static::$dictionaryModel, static::$order_field);
        }

        return ApiResponse::success(
            static::message(
                $item->wasRecentlyCreated ? static::$localizations['item created successfully'] : static::$localizations['item edited successfully'],
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
        $class = static::$dictionaryModel;

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

        ModelOrder::fix(static::$dictionaryModel, static::$order_field);

        return ApiResponse::success(static::message(static::$localizations['item deleted'], $item->{static::$name_field}))
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
        $class = static::$dictionaryModel;

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
            return ApiResponse::notFound(static::message(static::$localizations['item not found']));
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
                $item->{static::$enabled_field} ? static::$localizations['item switch on'] : static::$localizations['item switched off'],
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
                $class = static::$dictionaryModel;

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

            return ApiResponse::error("Ошибка обновления справочника " . static::$title)->payload(['exception' => $exception->getMessage()]);
        }

        return APIResponse::success("Справочник " . static::$title . " обновлён");
    }

}
