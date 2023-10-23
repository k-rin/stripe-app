<?php

namespace Tests\Unit\Providers;

use App\Providers\AppServiceProvider;
use Illuminate\Container\Container;
use Stripe\StripeClient;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_stripe_client_bind()
    {
        $container = new Container();
        $provider  = new AppServiceProvider($container);

        $provider->register();

        $this->assertTrue($container->bound(StripeClient::class));
    }
}