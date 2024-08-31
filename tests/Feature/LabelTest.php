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

    public function testLabelsPageCanBeRendered(): void
    {
        $response = $this->get('/labels');
        $response->assertStatus(200);
    }

    public function testLabelSinglePageCanBeRendered(): void
    {
        $label = Label::factory()->create();
        $response = $this->get("/labels/{$label->id}");
        $response->assertStatus(200);
    }

    public function testLabelCreatePageCanBeRendered(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/labels/create");
        $response->assertStatus(200);
    }

    public function testLabelEditPageCanBeRendered(): void
    {
        $user = User::factory()->create();
        $label = Label::factory()->create();
        $response = $this->actingAs($user)->get("/labels/{$label->id}/edit");
        $response->assertStatus(200);
    }



    public function testLabelCantBeCreateByQuest(): void
    {
        $response = $this->post('labels', [
            'name' => 'test label',
            'description' => 'test description',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('labels', ['name' => 'test label']);
    }

    public function testLabelCantBeDeletedByQuest(): void
    {
        $label = Label::factory()->create();
        $response = $this->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }

    public function testLabelCantBeUpdatedByQuest(): void
    {
        $label = Label::factory()->create();
        $response = $this->patch("/labels/{$label->id}", [
            'name' => 'test label',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('labels', ['id' => $label->id, 'name' => 'test label']);
    }

    public function testLabelCanBeCreatedByUser(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('labels', [
            'name' => 'test label',
            'description' => 'test description',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('labels', ['name' => 'test label']);
    }

    public function testLabelCanBeUpdatedByUser(): void
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

    public function testLabelCanBeDeletedByUser(): void
    {
        $label = Label::factory()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function testLabelsCanBeAttachedToTask(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $labels = Label::factory()->count(3)->create();

        $task->labels()->attach($labels->pluck('id')->toArray());   // @phpstan-ignore-line

        foreach ($labels as $label) {  // @phpstan-ignore-line
            $this->assertDatabaseHas('label_task', [
                'task_id' => $task->id,
                'label_id' => $label->id,  // @phpstan-ignore-line
            ]);
        }
    }

    public function testLabelsCanBeDetachedFromTask(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $labels = Label::factory()->count(3)->create();

        $task->labels()->attach($labels->pluck('id')->toArray());  // @phpstan-ignore-line

        $task->labels()->detach($labels->pluck('id')->toArray());  // @phpstan-ignore-line

        foreach ($labels as $label) {  // @phpstan-ignore-line
            $this->assertDatabaseMissing('label_task', [
                'task_id' => $task->id,
                'label_id' => $label->id,  // @phpstan-ignore-line
            ]);
        }
    }

    public function testTaskCanHaveLabels(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);  // @phpstan-ignore-line

        $this->assertTrue($task->labels->contains($label->id));  // @phpstan-ignore-line
    }

    public function testCascadeDelete(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);  // @phpstan-ignore-line

        $task->delete();  // @phpstan-ignore-line

        $this->assertDatabaseMissing('label_task', [
            'task_id' => $task->id,
            'label_id' => $label->id,
        ]);
    }

    public function testLabelCantBeDeletedIfUsed(): void
    {
        $taskStatus = TaskStatus::factory()->create();
        $user = User::factory()->create();
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);  // @phpstan-ignore-line

        $response = $this->actingAs($user)->delete(route('labels.destroy', $label->id));

        $response->assertRedirect(route('labels.index'));
        $response->assertSessionHas('flash_notification');
        $this->assertEquals(__('labels.The label cannot be deleted because it is associated with task.'), session('flash_notification')->first()->message);
    }
}
