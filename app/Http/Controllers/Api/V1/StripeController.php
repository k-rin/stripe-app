<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripeRequest;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;

class StripeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected StripeService $stripeService,
    ) {}

    /**
     * Checkout by given currency and amount.
     * 
     * @param  \App\Http\Requests\StripeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(StripeRequest $request): JsonResponse
    {
        try {
            $session = $this->stripeService->checkout(
                $request->input('currency'),
                $request->input('amount')
            );
        } catch (\Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                400
            );
        }

        return response()->json(
            ['url' => $session->url],
            200,
            [],
            JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
        );
    }
}
