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
    use RefreshDatabase;
    use WithFaker;

    public function testTasksScreenCanBeRendered(): void
    {
        $response = $this->get('/tasks');
        $response->assertStatus(200);
    }

    public function testTaskSingleScreenCanBeRendered(): void
    {
        TaskStatus::factory()->create();
        User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create();
        $response = $this->get("/tasks/{$task->id}");
        $response->assertStatus(200);
    }

    public function testTaskCreateScreenCanBeRenderedForUser(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/tasks/create");
        $response->assertStatus(200);
    }

    public function testTaskEditScreenCanBeRenderedForUser(): void
    {
        TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create();
        $response = $this->actingAs($user)->get("/tasks/{$task->id}/edit");
        $response->assertStatus(200);
    }


    public function testGuestCannotCreateTask(): void
    {
        $response = $this->get('/tasks/create', [
            'name' => 'Example Task',
            'status_id' => '1',
            'created_by_id' => '1'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('tasks', ['name' => 'Example Task']);
    }

    public function testGuestCannotEditTask(): void
    {
        TaskStatus::factory()->create();
        User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create();

        $response = $this->patch("/tasks/{$task->id}", [
            'name' => 'Update Task'
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tasks', ['name' => $task->name]);  //
        $this->assertDatabaseMissing('tasks', ['name' => 'Update Task']);
    }

    public function testGuestCannotDeleteTask(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->delete("tasks/{$task->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    public function testUserCanCreateTask(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
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

    public function testUserCanUpdateTask(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();
        /** @var TaskStatus $taskStatus */
        $taskStatus = TaskStatus::factory()->create();
        /** @var TaskStatus $taskStatus2 */
        $taskStatus2 = TaskStatus::factory()->create();
        /** @var Task $task * */
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

    public function testUserCanDeleteTask(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id]);

        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function testUserCantDeleteTaskIfHeIsNotTheCreator(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user1->id]);
        $response = $this->actingAs($user2)->delete("/tasks/{$task->id}");
        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}
