<?php

namespace Tests\Feature;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        /** @var Label $label */
        $label = Label::factory()->create();
        $response = $this->get("/labels/{$label->id}");
        $response->assertStatus(200);
    }

    public function testLabelCreatePageCanBeRendered(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get("/labels/create");
        $response->assertStatus(200);
    }

    public function testLabelEditPageCanBeRendered(): void
    {
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Label $label */
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
        /** @var Label $label */
        $label = Label::factory()->create();
        $response = $this->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseHas('labels', ['id' => $label->id]);
    }

    public function testLabelCantBeUpdatedByQuest(): void
    {
        /** @var Label $label */
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
        /** @var User $user * */
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
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Label $label */
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
        /** @var Label $label */
        $label = Label::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete("/labels/{$label->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function testLabelsCanBeAttachedToTask(): void
    {
        /** @var TaskStatus $taskStatus */
        $taskStatus = TaskStatus::factory()->create();

        /** @var User $user * */
        $user = User::factory()->create();

        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);

        /** @var Collection|Label[] $labels */
        $labels = Label::factory()->count(3)->create();

        $task->labels()->attach($labels->pluck('id')->toArray());

        foreach ($labels as $label) {
            /** @var Label $label */
            $this->assertDatabaseHas('label_task', [
                'task_id' => $task->id,
                'label_id' => $label->id,
            ]);
        }
    }

    public function testLabelsCanBeDetachedFromTask(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);

        /** @var Collection|Label[] $labels */
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

    public function testTaskCanHaveLabels(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        /** @var Label $label */
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $this->assertTrue($task->labels->contains($label->id));
    }

    public function testCascadeDelete(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        /** @var Label $label */
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $task->delete();

        $this->assertDatabaseMissing('label_task', [
            'task_id' => $task->id,
            'label_id' => $label->id,
        ]);
    }

    public function testLabelCantBeDeletedIfUsed(): void
    {
        /** @var TaskStatus $taskStatus * */
        $taskStatus = TaskStatus::factory()->create();
        /** @var User $user * */
        $user = User::factory()->create();
        /** @var Task $task * */
        $task = Task::factory()->create(['status_id' => $taskStatus->id, 'created_by_id' => $user->id]);
        /** @var Label $label */
        $label = Label::factory()->create();

        $task->labels()->attach($label->id);

        $response = $this->actingAs($user)->delete(route('labels.destroy', $label->id));

        $response->assertRedirect(route('labels.index'));
        $response->assertSessionHas('flash_notification');
        $this->assertEquals(__('labels.The label cannot be deleted because it is associated with task.'), session('flash_notification')->first()->message);
    }
}
