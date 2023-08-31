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
use App\Models\History\History;
use Illuminate\Database\Eloquent\Model;

/**
 * Common dictionary functionality.
 */
abstract class Dictionary
{
    protected static string $title = 'dictionaries/defaults.title';

    public static bool|array $viewPermissions = false;

    public static bool|array $editPermissions = false;

    protected static array $localizations = [
        'form_create_title' => 'dictionaries/defaults.form.create_title',
        'form_edit_title' => 'dictionaries/defaults.form.edit_title',
        'item_created_successfully' => 'dictionaries/defaults.messages.item_created_successfully',
        'item_edited_successfully' => 'dictionaries/defaults.messages.item_edited_successfully',
        'item_disabled' => 'dictionaries/defaults.messages.item_disabled',
        'item_enabled' => 'dictionaries/defaults.messages.item_enabled',
        'item_deleted' => 'dictionaries/defaults.messages.item_deleted',
        'item_not_found' => 'dictionaries/defaults.messages.item_not_found',
        'items_reordered' => 'dictionaries/defaults.messages.items_reordered',
        'dictionary_not_found' => 'dictionaries/defaults.messages.dictionary_not_found',
        'dictionary_forbidden' => 'dictionaries/defaults.messages.dictionary_forbidden',
    ];

    protected static array $fields = [
        'name' => [
            'title' => 'dictionaries/defaults.fields.name',
            'type' => 'string',
            'column' => 'name',
            'validation_rules' => 'required|unique',
            'validation_messages' => [
                'required' => 'validation.required',
                'unique' => 'validation.unique',
            ],
            'options' => [],
            'show' => true,
            'edit' => true,
        ],
    ];

    /**
     * Get dictionary title.
     *
     * @return string
     */
    public static function title(): string
    {
        return trans(static::$title);
    }

    /**
     * Get title for creating form.
     *
     * @return string
     */
    public static function titleFormCreate(): string
    {
        return trans(static::$localizations['form_create_title']);
    }

    /**
     * Get title for item edit form.
     *
     * @param string $name
     *
     * @return string
     */
    public static function titleFormEdit(string $name): string
    {
        return trans(static::$localizations['form_edit_title'], ['name' => $name]);
    }

    /**
     * Get item successfully created message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageDictionaryNotFound(string $name): string
    {
        return trans(static::$localizations['dictionary_not_found'], ['name' => $name]);
    }

    /**
     * Get item successfully created message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageDictionaryForbidden(string $name): string
    {
        return trans(static::$localizations['dictionary_forbidden'], ['name' => $name]);
    }

    /**
     * Get item successfully created message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageItemCreatedSuccessfully(string $name): string
    {
        return trans(static::$localizations['item_created_successfully'], ['name' => $name]);
    }

    /**
     * Get item successfully edited message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageItemEditedSuccessfully(string $name): string
    {
        return trans(static::$localizations['item_edited_successfully'], ['name' => $name]);
    }

    /**
     * Get item successfully disabled message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageItemDisabled(string $name): string
    {
        return trans(static::$localizations['item_disabled'], ['name' => $name]);
    }

    /**
     * Get item successfully enabled message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageItemEnabled(string $name): string
    {
        return trans(static::$localizations['item_enabled'], ['name' => $name]);
    }

    /**
     * Get item successfully deleted message.
     *
     * @param string $name
     *
     * @return string
     */
    public static function messageItemDeleted(string $name): string
    {
        return trans(static::$localizations['item_deleted'], ['name' => $name]);
    }

    /**
     * Get item not found message.
     *
     * @return string
     */
    public static function messageItemNotFound(): string
    {
        return trans(static::$localizations['item_not_found']);
    }

    /**
     * Get items successfully reordered message.
     *
     * @return string
     */
    public static function messageItemsReordered(): string
    {
        return trans(static::$localizations['items_reordered']);
    }

    /**
     * Get titles array for item edit form.
     *
     * @param bool $shown
     * @param bool $editable
     *
     * @return array
     */
    public static function fieldTitles(bool $shown, bool $editable): array
    {
        return array_filter(
            array_map(static function (array $record) use ($shown, $editable) {
                if (($shown && isset($record['show']) && $record['show']) || ($editable && isset($record['edit']) && $record['edit'])) {
                    return trans($record['title']);
                }
                return null;
            }, static::$fields)
        );
    }

    /**
     * Get optionally filtered types array.
     *
     * @param bool $shown
     * @param bool $editable
     *
     * @return array
     */
    public static function fieldTypes(bool $shown, bool $editable): array
    {
        return array_filter(
            array_map(static function (array $record) use ($shown, $editable) {
                if (($shown && isset($record['show']) && $record['show']) || ($editable && isset($record['edit']) && $record['edit'])) {
                    return trans($record['type']);
                }
                return null;
            }, static::$fields)
        );
    }

    /**
     * Get rules array for item validation.
     *
     * @return array
     */
    public static function fieldRules(): array
    {
        $rules = [];

        foreach (static::$fields as $key => $record) {
            if (array_key_exists('validation_rules', $record)) {
                $rules[$key] = $record['validation_rules'];
            }
        }

        return $rules;
    }

    /**
     * Get messages array for item validation.
     *
     * @return array
     */
    public static function fieldMessages(): array
    {
        $messages = [];

        foreach (static::$fields as $name => $record) {
            if (array_key_exists('validation_messages', $record)) {
                foreach ($record['validation_messages'] as $key => $message) {
                    $messages["$name.$key"] = trans($message);
                }
            }
        }

        return $messages;
    }


    /**
     * Get field options array.
     *
     * @return array
     */
    public static function fieldOptions(): array
    {
        return array_filter(
            array_map(static function (array $record) {
                return $record['options'] ?? null;
            }, static::$fields)
        );
    }

    /**
     * Check view ability.
     *
     * @param Current $current
     *
     * @return bool
     */
    public static function canView(Current $current): bool
    {
        return static::isAllowed(static::$viewPermissions, $current);
    }

    /**
     * Check edit ability.
     *
     * @param Current $current
     *
     * @return bool
     */
    public static function canEdit(Current $current): bool
    {
        return static::isAllowed(static::$editPermissions, $current);
    }

    /**
     * Check ability to view dictionary.
     *
     * @param array|bool|null $allow
     * @param Current $current
     *
     * @return  bool
     */
    private static function isAllowed(array|bool|null $allow, Current $current): bool
    {
        if (is_null($allow) || is_bool($allow)) {
            return !($allow === false);
        }

        foreach ($allow as $position => $permissions) {
            if (!$current->hasPositionType($position)) {
                continue;
            }
            if (is_bool($permissions)) {
                if ($permissions === true) {
                    return true;
                }
            } else {
                foreach ($permissions as $permission) {
                    if ($current->can($permission)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * todo refactor this
     *
     * Add history record for dictionary entry.
     *
     * @param string $alias
     * @param AbstractDictionary|Model $item
     * @param int $action_id
     * @param Current $current
     *
     * @return History
     */
//    protected static function addHistory(string $alias, AbstractDictionary|Model $item, int $action_id, Current $current): History
//    {
//        $history = new History([
//            'action_id' => $action_id,
//            'entry_title' => static::name() . ' "' . $item->{static::$name_field} . '"',
//            'entry_name' => get_class($item),
//            'entry_type' => 'dictionary_' . $alias,
//            'entry_id' => $item->{static::$id_field},
//            'position_id' => $current->positionId(),
//            'timestamp' => Carbon::now(),
//        ]);
//
//        $history->save();
//
//        return $history;
//    }
}
