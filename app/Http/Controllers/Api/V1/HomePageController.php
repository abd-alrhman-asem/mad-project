<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\VideoService;

class HomePageController extends Controller
{
    protected $videoService;

    public function __construct(VideoService $videoService)
    {
        $this->videoService = $videoService;
    }

    public function index()
    {
        $products = Product::all()->groupBy('type');
        $mediaBasePath = asset('media');

        $response = $products->map(function ($productGroup, $type) use ($mediaBasePath) {
            return [
                'video' => $this->videoService->getVideoPath($type, $mediaBasePath),
                'products' => ProductResource::collection($productGroup),
            ];
        });

        return response()->json($response);
    }
}