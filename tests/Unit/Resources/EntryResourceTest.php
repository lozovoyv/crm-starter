<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use Tests\TestCase;

class EntryResourceTest extends TestCase
{
    public function test_get_validation_rules(): void
    {
        $resource = new EntryResource();
        $this->assertEquals(
            [
                'name' => 'required',
                'active' => 'required',
                'description' => 'nullable',
            ],
            $resource->getValidationRules()
        );
    }

    public function test_get_titles(): void
    {
        $resource = new EntryResource();
        $this->assertEquals(
            [
                'name' => 'Название',
                'active' => 'Статус',
                'description' => 'Описание',
            ],
            $resource->getTitles()
        );
    }

    public function test_get_validation_messages(): void
    {
        $resource = new EntryResource();
        $this->assertEquals(
            [
                'name.required' => 'Обязательно',
                'active.required' => 'Обязательно',
            ],
            $resource->getValidationMessages()
        );
    }

    public function test_filter_data(): void
    {
        $resource = new EntryResource();
        $filtered = $resource->filterData(
            [
                'id' => 12,
                'name' => 'test',
                'active' => true,
                'description' => 'description',
            ],
            ['name', 'active', 'description']
        );

        self::assertEquals(
            [
                'name' => 'test',
                'active' => true,
                'description' => 'description',
            ],
            $filtered
        );
    }

    public function test_validate_data(): void
    {
        $resource = new EntryResource();
        $errors = $resource->validate([
            'name' => 'test',
            'active' => true,
            'description' => 'description',
        ]);

        $this->assertNull($errors);

        $errors = $resource->validate([
            'active' => true,
            'description' => 'description',
        ]);

        $this->assertEquals(
            ['name' => ['Обязательно']],
            $errors
        );
    }
}
