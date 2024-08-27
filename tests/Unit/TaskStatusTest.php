<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase;

    public function testTaskStatusHasName(): void
    {
        $taskStatus = TaskStatus::create([
            'name' => 'Done',
        ]);

        $this->assertEquals('Done', $taskStatus->name);
    }
}
