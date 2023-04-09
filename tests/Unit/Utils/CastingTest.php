<?php
declare(strict_types=1);

namespace Tests\Unit\Utils;

use App\Exceptions\CastingException;
use App\Utils\Casting;
use Carbon\Carbon;
use Tests\TestCase;

class CastingTest extends TestCase
{
    public function test_cast_to_string(): void
    {
        $this->assertEquals('test', Casting::fromString('test',Casting::string));
        $this->assertEquals('test', Casting::fromString('test',Casting::type_string));
        $this->assertEquals(null, Casting::fromString(null,Casting::type_string));
        $this->assertEquals(null, Casting::fromString('',Casting::type_string));
    }

    public function test_cast_to_int(): void
    {
        $this->assertEquals(1, Casting::fromString('1',Casting::int));
        $this->assertEquals(1, Casting::fromString('1',Casting::type_int));
        $this->assertEquals(-3, Casting::fromString('-3',Casting::type_string));
        $this->assertEquals(0, Casting::fromString('0',Casting::type_string));
        $this->assertEquals(null, Casting::fromString(null,Casting::type_string));
    }

    public function test_cast_to_bool(): void
    {
        $this->assertEquals(true, Casting::fromString('1',Casting::type_bool));
        $this->assertEquals(true, Casting::fromString('1',Casting::bool));
        $this->assertEquals(true, Casting::fromString('true',Casting::bool));
        $this->assertEquals(false, Casting::fromString('0',Casting::bool));
        $this->assertEquals(false, Casting::fromString('false',Casting::bool));
        $this->assertEquals(null, Casting::fromString('',Casting::bool));
        $this->assertEquals(null, Casting::fromString(null,Casting::bool));
    }

    public function test_cast_to_datetime(): void
    {
        $this->assertEquals('2023-04-09', Casting::fromString('2023-04-09',Casting::datetime)->format('Y-m-d'));
        $this->assertEquals('2023-04-09', Casting::fromString('2023-04-09',Casting::type_datetime)->format('Y-m-d'));
        $this->assertEquals('2023-04-09 13:49', Casting::fromString('2023-04-09T13:49',Casting::type_datetime)->format('Y-m-d H:i'));
        $this->assertEquals(null, Casting::fromString(null,Casting::datetime));
    }

    public function test_cast_to_array(): void
    {
        $this->assertEquals([], Casting::fromString('[]',Casting::type_array));
        $this->assertEquals([], Casting::fromString('[]',Casting::array));
        $this->assertEquals([1,2,3], Casting::fromString('[1,2,3]',Casting::array));
        $this->assertEquals(null, Casting::fromString('',Casting::array));
        $this->assertEquals(null, Casting::fromString(null,Casting::array));
        $this->expectException(CastingException::class);
        Casting::fromString('dq[1,2,3]',Casting::array);
    }

    public function test_cast_from_string(): void
    {
        $this->assertEquals('test', Casting::toString('test',Casting::string));
        $this->assertEquals('test', Casting::toString('test',Casting::type_string));
        $this->assertEquals(null, Casting::toString(null,Casting::type_string));
        $this->assertEquals(null, Casting::toString('',Casting::type_string));
        $this->expectException(CastingException::class);
        Casting::toString([1,2,3],Casting::string);
    }

    public function test_cast_from_int(): void
    {
        $this->assertEquals('1', Casting::toString(1,Casting::int));
        $this->assertEquals('1', Casting::toString(1,Casting::type_int));
        $this->assertEquals('-3', Casting::toString(-3,Casting::type_int));
        $this->assertEquals('0', Casting::toString(0,Casting::type_int));
        $this->assertEquals(null, Casting::toString(null,Casting::type_int));
    }

    public function test_cast_from_bool(): void
    {
        $this->assertEquals('1', Casting::toString(true,Casting::bool));
        $this->assertEquals('0', Casting::toString(false,Casting::bool));
        $this->assertEquals(null, Casting::toString(null,Casting::bool));
    }

    public function test_cast_from_datetime(): void
    {
        $this->assertEquals('2023-04-09 00:00:00', Casting::toString(Carbon::parse('2023-04-09'),Casting::datetime));
        $this->assertEquals('2023-04-09 00:00:00', Casting::toString(Carbon::parse('2023-04-09'),Casting::type_datetime));
        $this->assertEquals(null, Casting::toString(null,Casting::datetime));
    }

    public function test_cast_from_array(): void
    {
        $this->assertEquals(null, Casting::toString([],Casting::type_array));
        $this->assertEquals(null, Casting::toString([],Casting::array));
        $this->assertEquals('[1,2,3]', Casting::toString([1,2,3],Casting::array));
        $this->assertEquals(null, Casting::toString(null,Casting::array));
    }
}
