<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Media;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{

    use ApiResponsesTrait;

    /**
     * create new product
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable||numeric|min:0',
            'category_id' => 'required|exists:categories,id'
        ]);

        $validated['slug'] = $validated['slug'] ??  Str::slug($request->name);

        if ($request->hasFile('image')) {
            $media = new Media;
            $newImage = $media->upload($request->file('image'), 'products');
            $validated['image'] =  $newImage;
        };

        $product = Product::create($validated);

        return $this->successResponse(
            message: "product has been created successfully",
            data: [
                $product
            ],
            statusCode: 201
        );
    }

    /**
     * update a product
     */

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:available,unavailable',
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'price' => 'sometimes|numeric|min:0',
            'discount_price' => 'nullable||numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id'
        ]);

        $product = Product::findOrFail($id);
        $validated['slug'] = $request->slug ?: (isset($request->name) ? Str::slug($request->name) : $product->slug);


        if ($request->hasFile('image')) {
            $media = new Media;
            $newImage = $media->upload($request->image, 'products', $product->image);
            $validated['image'] =  $newImage;
        };

        $product->update($validated);

        return $this->successResponse(
            message: "product has been updated successfully",
            data: [
                $product
            ]
        );

    }

    /**
     * delete a product
     */


    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) {
            $media = new Media;
            $media->delete($product->image, 'products');
        }
        $product->delete();
        return $this->successResponse(message: 'product deleted successfully');
    }
}
