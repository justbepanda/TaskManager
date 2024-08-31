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

    public function testLabelCanBeCreated(): void
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

    public function testLabelCanBeUpdated(): void
    {
        $label = Label::factory()->create();

        $label->update([  // @phpstan-ignore-line
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);

        $this->assertDatabaseHas('labels', [
            'id' => $label->id,
            'name' => 'Updated Label',
            'description' => 'Updated Description',
        ]);
    }

    public function testExceptionDeleteALabelWithRelatedTasks(): void
    {
        $label = Label::factory()->create();
        User::factory()->create();
        TaskStatus::factory()->create();

        $task = Task::factory()->create();

        $label->tasks()->attach($task);  // @phpstan-ignore-line

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot delete label that has related tasks.");

        $label->delete();  // @phpstan-ignore-line
    }
}
