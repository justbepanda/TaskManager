<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_tasks_screen_can_be_rendered(): void
    {
        $response = $this->get('/tasks');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_create_task(): void
    {
        $response = $this->get('/tasks/create', [
            'name' => 'Example Task',
            'status_id' => '1',
            'created_by_id' => '1'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('tasks', ['name' => 'Example Task']);
    }

    public function test_guest_cannot_edit_task(): void
    {
        TaskStatus::factory()->create();
        User::factory()->create();
        $task = Task::factory()->create();

        $response = $this->patch("/tasks/{$task->id}", [
            'name' => 'Update Task'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tasks', ['name' => $task->name]);
        $this->assertDatabaseMissing('tasks', ['name' => 'Update Task']);
    }

    public function test_guest_cannot_delete_task(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->delete("tasks/{$task->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $response = $this->actingAs($user)->post('tasks', [
            'name' => 'Example Task',
            'status_id' => $taskStatus->id,
            'created_by_id' => $user->id,
            'description' => 'Example task description',
            'assigned_to_id' => $user->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', [
            'name' => 'Example Task',
            'description' => 'Example task description',
            'status_id' => $taskStatus->id,
            'created_by_id' => $user->id,
            'assigned_to_id' => $user->id
        ]);
    }

    public function test_user_can_update_task(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $taskStatus2 = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user1->id]);

        $response = $this->actingAs($user1)->patch("/tasks/{$task->id}", [
            'name' => 'Updated task',
            'status_id' => $taskStatus2->id,
            'description' => 'Updated task description',
            'assigned_to_id' => $user2->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'Updated task', 'status_id' => $taskStatus2->id, 'description' => 'Updated task description', 'assigned_to_id' => $user2->id]);
    }

    public function test_user_can_delete_task(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cant_edit_task_if_he_is_not_the_creator(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user1->id]);
        $response = $this->actingAs($user2)->patch("/tasks/{$task->id}", [
            'name' => 'Updated task',
            'status_id' => $taskStatus->id
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id, 'name' => 'Updated task']);
    }
}
