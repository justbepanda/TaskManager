<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\URL;

class AppServiceProviderTest extends TestCase
{
    public function testItForcesHttpsInProductionEnvironment(): void
    {
        $this->app['env'] = 'production';

        URL::shouldReceive('forceScheme')->once()->with('https');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();
    }
}
