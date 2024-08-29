<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_registers_task_policy()
    {
        $provider = new \App\Providers\AuthServiceProvider($this->app);

        $provider->boot();

        $policy = Gate::getPolicyFor(Task::class);
        $this->assertInstanceOf(TaskPolicy::class, $policy);
    }
}
