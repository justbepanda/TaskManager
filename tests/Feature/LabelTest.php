<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_labels_page_can_be_rendered()
    {
        $response = $this->get('/labels');
        $response->assertStatus(200);
    }

    public function test_label_single_page_can_be_rendered()
    {
        $label = Label::factory()->create();
        $response = $this->get("/labels/{$label->id}");
        $response->assertStatus(200);
    }

    public function test_label_cant_be_create_by_quest()
    {
        $response = $this->post('labels', [
            'name' => 'test label',
            'description' => 'test description',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('labels', ['name' => 'test label']);
    }

    public function test_label_cant_be_deleted_by_quest()
    {
        $label = Label::factory()->create();
        $response = $this->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }

    public function test_label_cant_be_updated_by_quest()
    {
        $label = Label::factory()->create();
        $response = $this->patch("/labels/{$label->id}", [
            'name' => 'test label',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('labels', ['id' => $label->id, 'name' => 'test label']);
    }

    public function test_label_can_be_created_by_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('labels', [
            'name' => 'test label',
            'description' => 'test description',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('labels', ['name' => 'test label']);
    }

    public function test_label_can_be_updated_by_user()
    {
        $user = User::factory()->create();
        $label = Label::factory()->create();

        $response = $this->actingAs($user)->patch('/labels/' . $label->id, [
            'name' => 'test label',
            'description' => 'test description',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('labels', ['name' => 'test label']);
    }

    public function test_label_can_be_deleted_by_user()
    {
        $label = Label::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function test_labels_can_be_attached_to_task()
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $labels = Label::factory()->count(3)->create();

        $task->labels()->attach($labels->pluck('id')->toArray());

        foreach ($labels as $label) {
            $this->assertDatabaseHas('label_task', [
                'task_id' => $task->id,
                'label_id' => $label->id,
            ]);
        }
    }

    public function test_labels_can_be_detached_from_task()
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $labels = Label::factory()->count(3)->create();

        $task->labels()->attach($labels->pluck('id')->toArray());

        $task->labels()->detach($labels->pluck('id')->toArray());

        foreach ($labels as $label) {
            $this->assertDatabaseMissing('label_task', [
                'task_id' => $task->id,
                'label_id' => $label->id,
            ]);
        }
    }

    public function test_task_can_have_labels()
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $this->assertTrue($task->labels->contains($label->id));
    }

    public function test_cascade_delete()
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $task->delete();

        $this->assertDatabaseMissing('label_task', [
            'task_id' => $task->id,
            'label_id' => $label->id,
        ]);
    }

    public function test_label_cant_be_deleted_if_used()
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $response = $this->actingAs($user)->delete(route('labels.destroy', $label->id));

        $response->assertRedirect(route('labels.index'));
        $response->assertSessionHas('flash_notification');
        $this->assertEquals(__('labels.The label cannot be deleted because it is associated with task.'), session('flash_notification')->first()->message);
    }
}
