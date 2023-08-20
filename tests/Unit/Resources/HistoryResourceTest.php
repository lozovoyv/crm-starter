<?php
declare(strict_types=1);

namespace Tests\Unit\Resources;

use App\Models\EntryScope;
use App\Models\History\History;
use App\Models\History\HistoryAction;
use App\Models\Positions\PositionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Assets\Resources\HistoryResource;
use Tests\Traits\CreatesUser;
use Tests\TestCase;

class HistoryResourceTest extends TestCase
{
    use CreatesUser, RefreshDatabase;

    protected function afterRefreshingDatabase(): void
    {
        $this->seed();
    }

    public function test_retrieve_record(): void
    {
        $user = $this->createUser('Тестовый', 'Тест', 'Тест');
        $admin = $this->createPosition($user, PositionType::admin);

        $history = $user->addHistory(HistoryAction::user_created, $admin->id, 'testing');

        $resource = new HistoryResource();
        $entry = $resource->forEntry($user->id)->retrieveRecord($history->id);
        $this->assertNotNull($entry);
        $this->assertEquals($user->id, $entry->entry_id);
        $this->assertEquals($admin->id, $entry->position_id);

        $resource = new HistoryResource();
        $entry = $resource->forOperator($admin->id)->retrieveRecord($history->id);
        $this->assertNotNull($entry);
        $this->assertEquals($user->id, $entry->entry_id);
        $this->assertEquals($admin->id, $entry->position_id);

        $resource = new HistoryResource();
        $entry = $resource->forEntry(100)->retrieveRecord($history->id);
        $this->assertNull($entry);

        $resource = new HistoryResource();
        $entry = $resource->forOperator(100)->retrieveRecord($history->id);
        $this->assertNull($entry);
    }

    public function test_filter(): void
    {
        $user = $this->createUser('Тестовый', 'Тест', 'Тест');
        $admin = $this->createPosition($user, PositionType::admin);

        $user->addHistory(HistoryAction::user_created, $admin->id, 'testing');
        $history = $user->addHistory(HistoryAction::user_deactivated, $admin->id, 'testing');
        $user->addHistory(HistoryAction::user_activated, $admin->id, 'testing');

        $resource = new HistoryResource();
        $entries = $resource->filter(['action_ids' => '[' . HistoryAction::user_deactivated . ']'])->get();
        $this->assertEquals(1, $entries->count());
        /** @var History $entry */
        $entry = $entries->first();
        $this->assertEquals($admin->id, $entry->position_id);
        $this->assertEquals($user->id, $entry->entry_id);
        $this->assertEquals(EntryScope::user, $entry->entry_name);
        $this->assertEquals(HistoryAction::user_deactivated, $entry->action_id);
    }

    public function test_get_list_titles(): void
    {
        $resource = new HistoryResource();

        $this->assertEquals([
            'timestamp' => 'Дата',
            'action' => 'Действие',
            'links' => 'Ссылки',
            'comment' => 'Комментарии',
            'changes' => 'Изменения',
            'position_id' => 'Оператор',
        ], $resource->getTitles());
    }

    public function test_get_orderable_columns(): void
    {
        $resource = new HistoryResource();

        $this->assertEquals([
            'timestamp',
        ], $resource->getOrderableColumns());
    }

    public function test_get_changes_titles(): void
    {
        $resource = new HistoryResource();

        $this->assertEquals([
            'Параметр',
            'Старое значение',
            'Новое значение',
        ], $resource->getChangesTitles());
    }
}
