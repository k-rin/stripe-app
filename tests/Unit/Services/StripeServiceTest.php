<?php

namespace Tests\Unit\Services;

use App\Services\StripeService;
use Stripe\StripeClient;
use Tests\TestCase;

class StripeServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->stripe = \Mockery::mock(StripeClient::class);
    }

    public function test_calculate_zero_decimal()
    {
        $service = new StripeService($this->stripe);

        $unitAmount = $service->getUnitAmount('JPY', 5000);

        $this->assertEquals(5000, $unitAmount);
    }

    public function test_calculate_two_decical()
    {
        $service = new StripeService($this->stripe);

        $unitAmount = $service->getUnitAmount('USD', 800.24);

        $this->assertEquals(80024, $unitAmount);
    }

    public function test_calculate_three_decimal()
    {
        $service = new StripeService($this->stripe);

        $unitAmount = $service->getUnitAmount('KWD', 5.125);

        $this->assertEquals(5130, $unitAmount);
    }

    public function test_calculate_special_case()
    {
        $service = new StripeService($this->stripe);

        $unitAmount = $service->getUnitAmount('TWD', 800.24);

        $this->assertEquals(80000, $unitAmount);
    }
}
