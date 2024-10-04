<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
class HomePageController extends Controller
{
    private const SECTION_VIDEOS = [
        'LandScaping' => 'landscaping.mp4',
        'Cladding' => 'cladding.mp4',
    ];

    public function index()
    {
        $products = Product::all()->groupBy('type');
        $mediaBasePath = asset('media');

        $response = $products->map(function ($productGroup, $type) use ($mediaBasePath) {
            return [
                'video' => $this->getVideoPath($type, $mediaBasePath),
                'products' => ProductResource::collection($productGroup)
            ];
        });

        return response()->json($response);
    }

    private function getVideoPath($type,$basePath)
    {
        return isset(self::SECTION_VIDEOS[$type]) 
            ? "{$basePath}/" . self::SECTION_VIDEOS[$type]
            : null;
    }
}