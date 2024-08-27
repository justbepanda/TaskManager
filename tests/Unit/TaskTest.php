<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Task;
use Tests\TestCase;
use App\Models\TaskStatus;

class TaskTest extends TestCase
{
    public function test_performer()
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['assigned_to_id' => $user->id, 'status_id' => $taskStatus->id]);

        $this->assertInstanceOf(User::class, $task->performer);
        $this->assertEquals($user->id, $task->performer->id);
    }

    public function test_creator()
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['created_by_id' => $user->id, 'status_id' => $taskStatus->id]);

        $this->assertInstanceOf(User::class, $task->creator);
        $this->assertEquals($user->id, $task->creator->id);
    }

    public function test_status()
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $this->assertInstanceOf(TaskStatus::class, $task->status);
        $this->assertEquals($user->id, $task->status->id);
    }

    public function test_task_can_be_created()
    {
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'This is a test task',
            'status_id' => 1,
            'created_by_id' => 1,
            'assigned_to_id' => 2,
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'description' => 'This is a test task',
            'status_id' => 1,
            'created_by_id' => 1,
            'assigned_to_id' => 2,
        ]);
    }

    public function test_task_can_be_updated()
    {
        $task = Task::factory()->create();

        $task->update([
            'name' => 'Updated Task Name',
            'description' => 'Updated Task Description',
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
            'description' => 'Updated Task Description',
        ]);
    }
}
