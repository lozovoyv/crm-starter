<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Support\Arr;

class APIRequest extends APIBaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get data from request.
     *
     * @param array|null $only
     *
     * @return array
     */
    public function data(?array $only = null): array
    {
        $data = $this->input('data', []);

        if ($only !== null) {
            $data = Arr::only($data, $only);
        }

        return $data;
    }

    /**
     * Get hash parameter from request.
     *
     * @return string|null
     */
    public function hash(): ?string
    {
        return $this->input('hash', null);
    }
}
