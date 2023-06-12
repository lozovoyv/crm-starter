<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class APIRequest extends FormRequest
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
     * @return array
     */
    public function data(): array
    {
        return $this->input('data', []);
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
