<?php
declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Assets\VDTO;
class VDTO extends \App\VDTO\VDTO
{
    protected array $rules = ['test' => 'required|string'];

    protected array $titles = ['test' => 'Test attribute'];

    protected array $messages = [
        'test.required' => 'Must be',
        'test.string' => 'Must be string',
    ];
}
