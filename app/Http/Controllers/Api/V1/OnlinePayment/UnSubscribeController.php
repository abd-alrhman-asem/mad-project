<?php

namespace App\Http\Controllers\Api\V1\OnlinePayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SubscriptionService;

class UnSubscribeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }
    public function __invoke(Request $request)
    {
        //
        $result = $this->subscriptionService->unsubscribe(auth()->id());

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }
}
