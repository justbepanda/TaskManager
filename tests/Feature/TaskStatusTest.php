<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_task_statuses_screen_can_be_rendered(): void
    {
        $response = $this->get('/task_statuses');
        $response->assertStatus(200);
    }

    public function test_guest_cant_create_task_status(): void
    {
        $response = $this->post('task_statuses', [
            'name' => 'Done'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('task_statuses', ['name' => 'Done']);
    }

    public function test_guest_cant_edit_task_status(): void
    {
        $taskStatus = TaskStatus::factory()->create();

        $response = $this->put("task_statuses/{$taskStatus->id}", [
            'name' => 'Update status',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('task_statuses', ['name' => 'Update status']);
    }

    public function test_guest_cant_delete_task_status(): void
    {
        $taskStatus = TaskStatus::factory()->create();

        $response = $this->delete("/task_statuses/{$taskStatus->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('task_statuses', ['id' => $taskStatus->id]);
    }

    public function test_auth_user_can_create_task_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('task_statuses', [
            'name' => 'Done'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_statuses', ['name' => 'Done']);
    }

    public function test_auth_user_can_edit_task_status(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create(['name' => 'pending']);
        $response = $this->actingAs($user)->put("task_statuses/{$taskStatus->id}", [
            'name' => 'Update status',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_statuses', ['name' => 'Update status']);
    }

    public function test_auth_user_can_delete_task_status(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create(['name' => 'pending']);
        $response = $this->actingAs($user)->delete("task_statuses/{$taskStatus->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('task_statuses', ['id' => $taskStatus->id]);
    }

    public function test_task_status_cannot_be_deleted_if_it_has_related_tasks(): void
    {
        $user = User::factory()->create();
        $taskStatus = TaskStatus::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->actingAs($user)->delete("/task_statuses/{$taskStatus->id}");

        $response->assertRedirect(route('task_statuses.index'));
        $response->assertSessionHas('flash_notification');
        $this->assertEquals(__('task_statuses.The status cannot be deleted because it is associated with tasks.'), session('flash_notification')->first()->message);
    }

    public function test_name_is_required_to_create_task_status()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/task_statuses', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('task_statuses', 0);
    }

    public function test_name_cannot_exceed_maximum_length()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/task_statuses', [
            'name' => str_repeat('A', 256),
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('task_statuses', 0);
    }
}
