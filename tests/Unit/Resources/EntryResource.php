<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use App\Resources\EntryResource as BaseEntryResource;

class EntryResource extends BaseEntryResource
{
    protected array $rules = [
        'name' => 'required',
        'active' => 'required',
        'description' => 'nullable',
    ];

    protected array $titles = [
        'name' => 'Название',
        'active' => 'Статус',
        'description' => 'Описание',
    ];

    protected array $messages = [
        'name.required' => 'Обязательно',
        'active.required' => 'Обязательно',
    ];

    public function validate($data): ?array
    {
        return $this->validateData($data, $this->rules, $this->titles, $this->messages);
    }
}
