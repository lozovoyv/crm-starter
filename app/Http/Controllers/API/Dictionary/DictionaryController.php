<?php
declare(strict_types=1);

namespace App\Http\Controllers\API\Dictionary;

use App\Current;
use App\Dictionaries\Base\Dictionary;
use App\Dictionaries\Base\DictionaryInterface;
use App\Exceptions\Dictionary\DictionaryForbiddenException;
use App\Exceptions\Dictionary\DictionaryNotFoundException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\APIListRequest;
use App\Http\Responses\ApiResponse;
use App\Permissions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class DictionaryController extends ApiController
{
    /**
     * Get dictionary by alias.
     *
     * @param string $alias
     *
     * @param APIListRequest $request
     * @return  ApiResponse
     */
    public function view(string $alias, APIListRequest $request): ApiResponse
    {
        try {
            /** @var DictionaryInterface $dictionaryClass */
            $dictionaryClass = $this->getDictionaryClass($alias);

            $current = Current::init($request);

            $dictionary = $dictionaryClass::view($current, $request->ifModifiedSince(), $request->filters(), $request->search());

        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }

        if ($dictionary->isNotModified()) {
            return ApiResponse::notModified();
        }

        return ApiResponse::list()
            ->items($dictionary->items())
            ->lastModified($dictionary->lastModified())
            ->payload([
                'is_editable' => $dictionary->isEditable(),
                'is_editor_available' => $current->can(Permissions::system__dictionaries),
            ]);
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

        $list = [];

        foreach ($dictionaries as $alias => $dictionary) {
            /** @var Dictionary $dictionary */

            if ($dictionary::canEdit($current)) {
                $list[] = [
                    'name' => $alias,
                    'title' => $dictionary::title()
                ];
            }
        }

        return ApiResponse::list()->items($list);
    }

    /**
     * Get dictionary items list for editor.
     *
     * @param string $alias
     * @param Request $request
     *
     * @return ApiResponse
     */
    public function list(string $alias, Request $request): ApiResponse
    {
        try {
            /** @var DictionaryInterface $dictionaryClass */
            $dictionaryClass = $this->getDictionaryClass($alias);

            $current = Current::init($request);

            $dictionary = $dictionaryClass::list($current);

        } catch (Exception $exception) {
            return $this->exceptionResponse($exception);
        }

        return ApiResponse::list()
            ->items($dictionary->items())
            ->title($dictionary->title())
            ->titles($dictionary->titles())
            ->payload([
                'orderable' => $dictionary->orderable(),
                'switchable' => $dictionary->switchable(),
                'types' => $dictionary->types(),
            ]);
    }

    /**
     * @param string|null $alias
     *
     * @return string
     *
     * @throws DictionaryNotFoundException
     * @throws Exception
     */
    protected function getDictionaryClass(?string $alias): string
    {
        $dictionaries = Config::get('dictionaries', []);

        if (!isset($dictionaries[$alias])) {
            throw new DictionaryNotFoundException(Dictionary::messageDictionaryNotFound($alias));
        }

        return $dictionaries[$alias];
    }

    /**
     * Make proper exception response.
     *
     * @param Exception $exception
     *
     * @return ApiResponse
     */
    protected function exceptionResponse(Exception $exception): ApiResponse
    {
        if (get_class($exception) === DictionaryNotFoundException::class) {
            return APIResponse::notFound($exception->getMessage());
        }

        if (get_class($exception) === DictionaryForbiddenException::class) {
            return APIResponse::forbidden($exception->getMessage());
        }

        return ApiResponse::serverError($exception);
    }



    /**
     * TODO refactor this:
     */


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

}
