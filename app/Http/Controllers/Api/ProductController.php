<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponsesTrait;

    /**
     * get all products
     */

    public function index() {
        $products = Product::with('category')->where('status', 'available')->get();
        return $this->successResponse(
            data: [
                $products
            ],
        );



    }

    /**
     * get specific product
     */
    public function show($id) {
        $product = Product::with('category')->findOrFail($id);
        return $this->successResponse(
            data: [
                $product
            ],
        );

    }




}
