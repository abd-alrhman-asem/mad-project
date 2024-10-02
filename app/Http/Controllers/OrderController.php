<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use App\Traits\ApiResponseHandlerTrait;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    use ApiResponseHandlerTrait;
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
         $this->orderService->createOrder($request->validated());
        return $this->successMessage('Order created successfully');
    }

}
