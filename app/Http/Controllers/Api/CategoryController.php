<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

use ApiResponsesTrait;
    /**
     * list all active categories
     *
     */

    public function index() {
        $categories = Category::where('is_active', true)->get();
        return $this->successResponse(
            data : [
                'categories' => $categories,
            ]
        );
    }

    /**
     * Get category with its products
     */

    public function show(string $id) {
        $category = Category::where('is_active', true)->findOrFail($id);
        return $this->successResponse(
            data: [
                'category' => $category
            ]
        );
    }
}
