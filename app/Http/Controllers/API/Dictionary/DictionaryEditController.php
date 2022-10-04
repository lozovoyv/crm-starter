<?php

namespace App\Http\Controllers\API\Dictionary;

use App\Foundation\Dictionaries\AbstractDictionary;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DictionaryEditController extends ApiController
{
    use EditableDictionaries;

    /**
     * Get editable dictionaries list.
     *
     * @return  JsonResponse
     */
    public function index(): JsonResponse
    {
        return APIResponse::response(array_map(static function ($item) {
            return $item['name'];
        }, $this->dictionaries), []);
    }

    /**
     * Get details for selected dictionary.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function details(Request $request): JsonResponse
    {
        $name = $request->input('name');

        if (!array_key_exists($name, $this->dictionaries)) {
            return APIResponse::notFound("Словарь $name не найден.");
        }

        /** @var \App\Foundation\Dictionaries\AbstractDictionary $class */
        $class = $this->dictionaries[$name]['class'];

        $all = $class::query()->orderBy('order')->orderBy('name')->get();

        return APIResponse::response([
            'items' => $all,
            'item_name' => $this->dictionaries[$name]['item_name'],
            'fields' => $this->dictionaries[$name]['fields'],
            'titles' => $this->dictionaries[$name]['titles'],
            'validation' => $this->dictionaries[$name]['validation'],
            'hide' => $this->dictionaries[$name]['hide'] ?? null,
            'auto' => $this->dictionaries[$name]['auto'] ?? null,
        ]);
    }

    /**
     * Update order and status.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function sync(Request $request): JsonResponse
    {
        $name = $request->input('name');

        if (!array_key_exists($name, $this->dictionaries)) {
            return APIResponse::notFound("Словарь $name не найден.");
        }

        /** @var \App\Foundation\Dictionaries\AbstractDictionary $class */
        $class = $this->dictionaries[$name]['class'];

        $data = $this->data($request);

        foreach ($data as $item) {
            $class::query()->where('id', $item['id'])->update(['order' => $item['order'], 'enabled' => $item['enabled']]);
        }

        return APIResponse::success("Справочник $name обновлён");
    }

    /**
     * Create or update dictionary item.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $name = $request->input('name');

        if (!array_key_exists($name, $this->dictionaries)) {
            return APIResponse::notFound("Словарь $name не найден.");
        }

        /** @var \App\Foundation\Dictionaries\AbstractDictionary $class */
        $class = $this->dictionaries[$name]['class'];
        $title = $this->dictionaries[$name]['name'];

        $fields = $this->dictionaries[$name]['fields'];
        $titles = $this->dictionaries[$name]['titles'];
        $validation = $this->dictionaries[$name]['validation'];

        $data = $this->data($request);
        $data = array_intersect_key($data, $fields);

        if ($errors = $this->validate($data, $validation, $titles)) {
            return APIResponse::validationError($errors);
        }

        /** @var \App\Foundation\Dictionaries\AbstractDictionary $item */
        $item = $this->firstOrNew($class, $request->input('id'));

        if ($item === null) {
            return APIResponse::notFound("Запись в словаре \"$title\" не найдена");
        }

        foreach ($data as $key => $value) {
            $item->setAttribute($key, $value);
        }

        if (!$item->exists) {
            $order = (int)$class::query()->max('order') + 1;
            $item->order = $order;
        }

        $item->save();
        $item->refresh();

        return APIResponse::success($item->wasRecentlyCreated ? "Запись в словаре \"$title\" добавлена" : "Запись в словаре \"$title\" обновлена",
            $item->toArray()
        );
    }
}
