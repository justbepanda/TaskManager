<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Tests\TestCase;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function testTaskStatusHasName(): void
    {
        $taskStatus = TaskStatus::create([
            'name' => 'Done',
        ]);

        $this->assertEquals('Done', $taskStatus->name);
    }
    public function test_exception_delete_a_label_with_related_tasks()
    {
        User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();

        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot delete status that has related tasks.");

        $taskStatus->delete();
    }
}
