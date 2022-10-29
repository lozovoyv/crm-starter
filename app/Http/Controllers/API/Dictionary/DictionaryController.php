<?php

namespace App\Http\Controllers\API\Dictionary;

use App\Current;
use App\Foundation\Dictionaries\AbstractDictionary;
use App\Http\APIResponse;
use App\Http\Controllers\ApiController;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DictionaryController extends ApiController
{
    protected array $dictionaries;

    /**
     * Get dictionary.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function getDictionary(Request $request): JsonResponse
    {
        $this->dictionaries = require base_path('/app/dictionaries.php');

        $name = $request->input('dictionary');

        if ($name === null || !array_key_exists($name, $this->dictionaries)) {
            return APIResponse::notFound("Справочник $name не найден");
        }

        $dictionary = $this->dictionaries[$name];

        $current = Current::get($request);

        if (array_key_exists('allow', $dictionary) && !$this->isAllowed($dictionary['allow'], $current)) {
            return APIResponse::forbidden("Нет прав на просмотр справочника $name");
        }

        /** @var Model $class */
        $class = $dictionary['class'];

        $query = $class::query();

        $actual = $query->clone()->latest('updated_at')->value('updated_at');
        $actual = Carbon::parse($actual)->setTimezone('GMT');

        if ($request->hasHeader('If-Modified-Since')) {
            $requested = Carbon::createFromFormat('D\, d M Y H:i:s \G\M\T', $request->header('If-Modified-Since'), 'GMT');
            if ($requested >= $actual) {
                return APIResponse::notModified();
            }
        }

        $dictionary = $query->orderBy('order')->orderBy('name')->get();

        return APIResponse::response($dictionary, null, null, $actual ?? null);
    }

    /**
     * Check ability to view dictionary.
     *
     * @param array|null $allow
     * @param Current $current
     *
     * @return  bool
     */
    public function isAllowed(?array $allow, Current $current): bool
    {
        if (empty($allow)) {
            return true;
        }

        foreach ($allow as $position => $permissions) {
            if (!$current->hasPositionType($position)) {
                continue;
            }
            foreach ($permissions as $permission) {
                if ($current->can($permission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
