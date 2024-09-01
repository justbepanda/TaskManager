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

    public function testTaskStatusesScreenCanBeRendered(): void
    {
        $response = $this->get('/task_statuses');
        $response->assertStatus(200);
    }


    public function testTaskStatusSingleScreenCanBeRendered(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        $response = $this->get("/task_statuses/{$taskStatus->id}");
        $response->assertStatus(200);
    }

    public function testTaskStatusCreateScreenCanBeRendered(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/task_statuses/create');
        $response->assertStatus(200);
    }

    public function testTaskStatusEditScreenCanBeRendered(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/task_statuses/{$taskStatus->id}/edit");
        $response->assertStatus(200);
    }

    public function testGuestCantCreateTaskStatus(): void
    {
        $response = $this->post('task_statuses', [
            'name' => 'Done'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('task_statuses', ['name' => 'Done']);
    }

    public function testGuestCantEditTaskStatus(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();

        $response = $this->put("task_statuses/{$taskStatus->id}", [
            'name' => 'Update status',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('task_statuses', ['name' => 'Update status']);
    }

    public function testGuestCantDeleteTaskStatus(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();

        $response = $this->delete("/task_statuses/{$taskStatus->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('task_statuses', ['id' => $taskStatus->id]);
    }

    public function testAuthUserCanCreateTaskStatus(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('task_statuses', [
            'name' => 'Done'
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_statuses', ['name' => 'Done']);
    }

    public function testAuthUserCanEditTaskStatus(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create(['name' => 'pending']);
        $response = $this->actingAs($user)->put("task_statuses/{$taskStatus->id}", [
            'name' => 'Update status',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_statuses', ['name' => 'Update status']);
    }

    public function testAuthUserCanDeleteTaskStatus(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create(['name' => 'pending']);
        $response = $this->actingAs($user)->delete("task_statuses/{$taskStatus->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('task_statuses', ['id' => $taskStatus->id]);
    }

    public function testTaskStatusCannotBeDeletedIfItHasRelatedTasks(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->actingAs($user)->delete("/task_statuses/{$taskStatus->id}");

        $response->assertRedirect(route('task_statuses.index'));
        $response->assertSessionHas('flash_notification');
        $this->assertEquals(__('task_statuses.The status cannot be deleted because it is associated with tasks.'), session('flash_notification')->first()->message);
    }

    public function testNameIsRequiredToCreateTaskStatus()
    {
        /** @var User $user * */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/task_statuses', [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('task_statuses', 0);
    }

    public function testNameCannotExceedMaximumLength()
    {
        /** @var User $user * */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/task_statuses', [
            'name' => str_repeat('A', 256),
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('task_statuses', 0);
    }
}
