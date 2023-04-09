<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Dictionary;

use App\Current;
use App\Dictionaries\Dictionary;
use App\Dictionaries\DictionaryInterface;
use App\Exceptions\Dictionary\DictionaryForbiddenException;
use App\Exceptions\Dictionary\DictionaryNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Responses\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DictionaryController extends ApiController
{
    /**
     * Get dictionary by alias.
     *
     * @param Request $request
     * @param string $alias
     *
     * @return  ApiResponse
     */
    public function view(Request $request, string $alias): ApiResponse
    {
        $current = Current::init($request);

        try {
            $dictionaryRecord = $this->getDictionaryRecord($alias, $current);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        $isEditable = array_key_exists('edit', $dictionaryRecord) && $this->isAllowed($dictionaryRecord['edit'], $current);

        $ifModifiedSince = $request->hasHeader('If-Modified-Since')
            ? Carbon::createFromFormat('D\, d M Y H:i:s \G\M\T', $request->header('If-Modified-Since'), 'GMT')
            : null;

        /** @var DictionaryInterface $class */
        $class = $dictionaryRecord['class'];

        $dictionary = $class::view($isEditable, $ifModifiedSince, $request->input('filters', []), $request->input('search'));

        if ($dictionary->isNotModified()) {
            return ApiResponse::notModified();
        }

        return ApiResponse::list()
            ->items($dictionary->items())
            ->lastModified($dictionary->lastModified())
            ->payload(['is_editable' => $isEditable]);
    }

    /**
     * Get editable dictionaries list.
     *
     * @param Request $request
     *
     * @return  ApiResponse
     */
    public function index(Request $request): ApiResponse
    {
        $current = Current::init($request);

        $dictionaries = Config::get('dictionaries', []);

        $editable = [];

        foreach ($dictionaries as $alias => $dictionary) {
            /** @var Dictionary $class */
            $class = $dictionary['class'];
            if (!array_key_exists('edit', $dictionary) || $this->isAllowed($dictionary['edit'], $current)) {
                $editable[] = [
                    'name' => $alias,
                    'title' => $class::title(),
                ];
            }
        }

        return ApiResponse::list()->items($editable);
    }

    /**
     * Get dictionary items list for editor.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function list(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionaryRecord = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionaryRecord['class'];

        $dictionary = $class::list();

        return ApiResponse::list()
            ->items($dictionary->items())
            ->title($dictionary->title())
            ->titles($dictionary->titles())
            ->payload([
                'orderable' => $dictionary->orderable(),
                'switchable' => $dictionary->switchable(),
                'fields' => $dictionary->fields(),
            ]);
    }

    /**
     * Get editing data for dictionary entry.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function get(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionary = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionary['class'];

        return $class::get($request);
    }

    /**
     * Update dictionary item.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function update(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionary = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionary['class'];

        return $class::update($request, $name);
    }

    /**
     * Delete dictionary item.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function delete(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionary = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionary['class'];

        return $class::delete($request, $name);
    }

    /**
     * Toggle dictionary item state.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function toggle(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionary = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionary['class'];

        return $class::toggle($request, $name);
    }

    /**
     * Sync items order for dictionary.
     *
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function sync(Request $request): ApiResponse
    {
        $name = $request->input('dictionary');
        $current = Current::init($request);

        try {
            $dictionary = $this->getDictionaryRecord($name, $current, false, true);
        } catch (DictionaryNotFoundException $exception) {
            return APIResponse::notFound($exception->getMessage());
        } catch (DictionaryForbiddenException $exception) {
            return APIResponse::forbidden($exception->getMessage());
        }

        /** @var Dictionary $class */
        $class = $dictionary['class'];

        return $class::sync($request);
    }

    /**
     * Get dictionary class with permission checks.
     *
     * @param string|null $name
     * @param Current $current
     * @param bool $checkView
     * @param bool $checkEdit
     *
     * @return array
     *
     * @throws DictionaryNotFoundException
     * @throws DictionaryForbiddenException
     */
    protected function getDictionaryRecord(?string $name, Current $current, bool $checkView = true, bool $checkEdit = false): array
    {
        $dictionaries = Config::get('dictionaries', []);

        if ($name === null || !array_key_exists($name, $dictionaries)) {
            throw new DictionaryNotFoundException("Справочник $name не найден");
        }

        $dictionary = $dictionaries[$name];

        if ($checkView && array_key_exists('view', $dictionary) && !$this->isAllowed($dictionary['view'], $current)) {
            throw new DictionaryForbiddenException("Нет прав на просмотр справочника $name");
        }
        if ($checkEdit && array_key_exists('edit', $dictionary) && !$this->isAllowed($dictionary['edit'], $current)) {
            throw new DictionaryForbiddenException("Нет прав на редактирование справочника $name");
        }

        return $dictionary;
    }

    /**
     * Check ability to view dictionary.
     *
     * @param array|bool|null $allow
     * @param Current $current
     *
     * @return  bool
     */
    protected function isAllowed(array|bool|null $allow, Current $current): bool
    {
        if (empty($allow) || is_bool($allow)) {
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
}
