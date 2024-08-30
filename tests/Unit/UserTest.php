<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\TaskStatus;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testCreatedTasks(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['created_by_id' => $user->id, 'status_id' => $taskStatus->id]);

        $this->assertTrue($user->createdTasks->contains($task));
    }

    public function testAssignedTasks(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'assigned_to_id' => $user->id]);

        $this->assertTrue($user->assignedTasks->contains($task));
    }
}
