<?php
declare(strict_types=1);

namespace Tests\Unit\VDTO;

use Tests\Assets\VDTO\VDTO;
use Tests\TestCase;

class VDTOTest extends TestCase
{
    public function test_vdto_attributes(): void
    {
        $vdto = new VDTO(['test' => 123]);

        $this->assertEquals(123, $vdto->test);
        $this->assertEquals(123, $vdto->getAttribute('test'));

        $vdto->setAttribute('test', '456');
        $this->assertEquals('456', $vdto->getAttribute('test'));

        $vdto->test = '789';
        $this->assertEquals('789', $vdto->getAttribute('test'));

        $this->assertFalse(isset($vdto->testUndefined));
        $this->assertNull($vdto->testUndefined);
        $this->assertNull($vdto->getAttribute('testUndefined'));
    }

    public function test_vdto_titles(): void
    {
        $vdto = new VDTO();
        $this->assertEquals(['test' => 'Test attribute'], $vdto->getTitles());
        $this->assertEquals(['test' => 'Test attribute'], $vdto->getTitles(['test']));
        $this->assertEquals([], $vdto->getTitles(['not_exists']));
    }

    public function test_vdto_validation_messages(): void
    {
        $vdto = new VDTO();
        $this->assertEquals([
            'test.required' => 'Must be',
            'test.string' => 'Must be string',
        ], $vdto->getValidationMessages());
        $this->assertEquals([
            'test.required' => 'Must be',
            'test.string' => 'Must be string',
        ], $vdto->getValidationMessages(['test']));
        $this->assertEquals([], $vdto->getValidationMessages(['not_exists']));
    }

    public function test_vdto_validation_rules(): void
    {
        $vdto = new VDTO();
        $this->assertEquals(['test' => 'required|string'], $vdto->getValidationRules());
        $this->assertEquals(['test' => 'required|string'], $vdto->getValidationRules(['test']));
        $this->assertEquals([], $vdto->getValidationRules(['not_exists']));
    }

    public function test_vdto_validate_fails(): void
    {
        $vdto = new VDTO(['test' => 123]);

        $this->assertEquals(['test' => ['Must be string']], $vdto->validate());

        unset($vdto->test);

        $this->assertEquals(['test' => ['Must be']], $vdto->validate());
    }

    public function test_vdto_validate_success(): void
    {
        $vdto = new VDTO(['test' => '123']);

        $this->assertNull($vdto->validate());

        unset($vdto->test);

        $this->assertNull($vdto->validate(['not_exists']));
    }
}
