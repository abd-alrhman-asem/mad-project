<?php

namespace App\Http\Controllers\Api\V1\OnlinePayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripePaymentService;
use App\Services\WebhookService;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $webhookService;

    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function handleWebhook(Request $request)
    {
        try {
            $event = $this->webhookService->verifyWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                env('STRIPE_WEBHOOK_SECRET')
            );

            $this->webhookService->handleWebhookEvent($event);

            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
