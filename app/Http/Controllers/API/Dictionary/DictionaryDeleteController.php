<?php

namespace App\Http\Controllers\API\Dictionary;

use App\Foundation\Dictionaries\AbstractDictionary;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DictionaryDeleteController extends ApiController
{
    use EditableDictionaries;

    /**
     * Delete dictionary item.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $name = $request->input('name');

        if (!array_key_exists($name, $this->dictionaries)) {
            return APIResponse::notFound("Словарь $name не найден.");
        }

        /** @var AbstractDictionary $class */
        $class = $this->dictionaries[$name]['class'];
        $title = $this->dictionaries[$name]['name'];

        if (null === ($item = $class::query()->where('id', $id)->first())) {
            return APIResponse::notFound("Запись в словаре $title не найдена.");
        }
        /** @var AbstractDictionary $item */

        $name = $item->name;

        if($item->getAttribute('locked') === true) {
            return APIResponse::error("Запись \"$name\" в словаре \"$title\" является системной.");
        }

        try {
            $class::query()->where('id', $id)->delete();
        } catch (QueryException $exception) {
            return APIResponse::error("Невозможно удалить запись \"$name\" в словаре \"$title\". Есть блокирующие связи.");
        } catch (Exception $exception) {
            return APIResponse::error($exception->getMessage());
        }

        return APIResponse::response([], [], "Запись \"$name\" в словаре \"{$title}\" удалена");
    }
}
