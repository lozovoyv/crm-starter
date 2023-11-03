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

namespace Tests\Unit\User;

use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PositionTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_position_type(): void
    {
        $types = PositionType::query()->pluck('id')->toArray();
        $this->assertGreaterThan(0, count($types));

        foreach ($types as $type) {
            $this->assertIsInt($type);
            $this->assertIsString(PositionType::typeToString($type));
        }

        $this->expectException(InvalidArgumentException::class);
        PositionType::typeToString(max($types) + 1);
    }
}
