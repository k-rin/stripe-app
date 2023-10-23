<?php

namespace Tests\Unit\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\StripeController;
use App\Http\Requests\StripeRequest;
use App\Services\StripeService;
use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;

class StripeControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->stripeService = \Mockery::mock(StripeService::class);
    }

    public function test_checkout_normal_end()
    {
        $controller = new StripeController($this->stripeService);

        $data = [
            'currency' => 'JPY',
            'amount'   => 5000,
        ];
        $session = (object) [
            'url' => 'checkout_url',
        ];

        $request = new StripeRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag($data));

        $this->stripeService->shouldReceive('checkout')
            ->withArgs(['JPY', 5000])
            ->andReturn($session);

        $response = $controller->checkout($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('checkout_url', $response->getData()->url);
        $this->assertEquals(200, $response->status());
    }

    public function test_checkout_throw_exception()
    {
        $controller = new StripeController($this->stripeService);

        $data = [
            'currency' => 'USD',
            'amount'   => 5000.11,
        ];
        $exception = new \Exception('error message');

        $request = new StripeRequest();
        $request->headers->set('content-type', 'application/json');
        $request->setJson(new ParameterBag($data));

        $this->stripeService->shouldReceive('checkout')
            ->withArgs(['USD', 5000.11])
            ->andThrow($exception);

        $response = $controller->checkout($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals('error message', $response->getData()->message);
        $this->assertEquals(400, $response->status());
    }

    public function test_checkout_without_currency()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'amount' => 5000,
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_with_integer_currency()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 123,
            'amount'   => 5000,
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_with_invalid_char_currency()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => '123',
            'amount'   => 5000,
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_with_over_three_digit_currency()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 'TEST',
            'amount'   => 5000,
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_without_amount()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 'JPY',
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_with_string_amount()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 'JPY',
            'amount'   => 'TEST',
        ]);

        $response->assertStatus(422);
    }

    public function test_checkout_with_integer_amount_in_decimal()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 'JPY',
            'amount'   => 5000.11,
        ]);

        $response->assertStatus(422);
    }
    public function test_checkout_with_over_three_decimal_amount()
    {
        $response = $this->postJson(action([StripeController::class, 'checkout']), [
            'currency' => 'USD',
            'amount'   => 5000.1111,
        ]);

        $response->assertStatus(422);
    }
}
