<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testTaskStatusHasName(): void
    {
        $taskStatus = TaskStatus::factory()->create([
            'name' => 'Done',
        ]);

        $this->assertEquals('Done', $taskStatus->name);
    }
    public function testExceptionDeleteALabelWithRelatedTasks(): void
    {
        User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();

        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot delete status that has related tasks.");

        $taskStatus->delete(); // @phpstan-ignore-line
    }
}
