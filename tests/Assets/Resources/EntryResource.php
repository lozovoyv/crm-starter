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

namespace Tests\Assets\Resources;

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
