<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiResponseForm extends ApiResponse
{
    protected int $statusCode = self::CODE_OK;

    protected ?array $values;
    protected ?string $hash;
    protected ?array $rules;
    protected ?array $messages;
    protected ?string $title;
    protected ?array $titles;

    /**
     * Get response.
     *
     * @param Request $request
     *
     * @return  JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse(
            static::combine([
                'values' => $this->values ?? [],
            ], [
                'title' => $this->title ?? null,
                'titles' => $this->titles ?? [],
                'rules' => $this->rules ?? [],
                'messages' => $this->messages ?? [],
                'hash' => $this->hash ?? null,
                'message' => $this->message ?? null,
                'payload' => $this->payload ?? [],
            ]), $this->statusCode, $this->getHeaders()
        );
    }

    /**
     * Form title.
     *
     * @param string|null $title
     *
     * @return  $this
     */
    public function title(?string $title): ApiResponseForm
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Field titles.
     *
     * @param array|null $titles
     *
     * @return  $this
     */
    public function titles(?array $titles): ApiResponseForm
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * Field values.
     *
     * @param array|null $values
     *
     * @return  $this
     */
    public function values(?array $values): ApiResponseForm
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Field validation rules.
     *
     * @param array|null $rules
     *
     * @return  $this
     */
    public function rules(?array $rules): ApiResponseForm
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Field custom error validation messages.
     *
     * @param array|null $messages
     *
     * @return  $this
     */
    public function messages(?array $messages): ApiResponseForm
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Form hash.
     *
     * @param string|null $hash
     *
     * @return  $this
     */
    public function hash(?string $hash): ApiResponseForm
    {
        $this->hash = $hash;

        return $this;
    }
}
