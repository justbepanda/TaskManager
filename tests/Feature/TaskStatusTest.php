<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TaskStatusTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_task_statuses_screen_can_be_rendered(): void
    {
        $response = $this->get('/task_statuses');
        $response->assertStatus(200);
    }
    public function test_create_task_status(): void
    {
        $response = $this->post('task_statuses', [
            'name' => 'Done'
        ]);

        $response->assertStatus(302);
    }
}
