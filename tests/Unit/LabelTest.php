<?php

namespace Tests\Unit;

use App\Models\Label;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_label_can_be_created()
    {
        $label = Label::factory()->create([
            'name' => 'Example Label',
            'description' => 'Example Description',
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'name' => 'Example Label',
            'description' => 'Example Description',
        ]);
    }

    public function test_label_can_be_updated()
    {
        $label = Label::factory()->create();

        $label->update([
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);
    }

    public function test_exception_delete_a_label_with_related_tasks()
    {
        $label = Label::factory()->create();
        User::factory()->create();
        TaskStatus::factory()->create();

        $task = Task::factory()->create();

        $label->tasks()->attach($task);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot delete label that has related tasks.");

        $label->delete();
    }
}
