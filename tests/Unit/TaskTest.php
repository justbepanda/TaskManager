<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\TaskStatus;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function testPerformer(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();

        /** @var Task $task * */
        $task = Task::factory()->create(['created_by_id' => $user->id, 'assigned_to_id' => $user->id, 'status_id' => $taskStatus->id]);

        $this->assertInstanceOf(User::class, $task->performer);
        $this->assertEquals($user->id, $task->performer->id);
    }

    public function testCreator(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['created_by_id' => $user->id, 'status_id' => $taskStatus->id]);

        $this->assertInstanceOf(User::class, $task->creator);
        $this->assertEquals($user->id, $task->creator->id);
    }

    public function testStatus(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $this->assertEquals($user->id, $task->status->id);
    }

    public function testTaskCanBeCreated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task */
        $task = Task::factory()->create([
            'name' => 'Test Task',
            'description' => 'This is a test task',
            'status_id' => $taskStatus->id,
            'created_by_id' => $user->id,
            'assigned_to_id' => $user->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'name' => 'Test Task',
            'description' => 'This is a test task',
            'status_id' => $taskStatus->id,
            'created_by_id' => $user->id,
            'assigned_to_id' => $user->id,
        ]);
    }

    public function testTaskCanBeUpdated(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['created_by_id' => $user->id, 'status_id' => $taskStatus->id]);

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
