<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Media;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AdminCategoryController extends Controller
{
    use ApiResponsesTrait;

    /**
     * create new Category
     */

    public function store(Request $request)
    {
        $validated =  $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $request->slug ?? Str::slug($request->name);

        if ($request->hasFile('image')) {
            $media = new Media;
            $newImage = $media->upload($request->file('image'), 'categories');
            $validated['image'] = $newImage;
        }

        $category = Category::create($validated);

        return $this->successResponse(
            message: 'Category created successfully',
            data: [
                'category' => $category,
            ],
            statusCode: 201
        );
    }

    /**
     *  update a category
     */

    public function update(Request $request, string $id)
    {
        $validated =  $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'sometimes|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);
        $category = Category::findOrFail($id);

        $validated['slug'] = $request->slug ?: (isset($request->name) ? Str::slug($request->name) : $category->slug);

        if ($request->hasFile('image')) {
            $media = new Media;
            $newImage = $media->upload($request->file('image'), 'categories', $category->image);
            $validated['image'] = $newImage;
        };
        $category->update($validated);
        return $this->successResponse(
            message: "category has been updated successfully",
            data: [
                'category' => $category,
            ]
        );
    }


    /**
     * delete a category
     */

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        if ($category->image) {
            $media = New Media;
            $media->delete( $category->image , 'categories');
        }
        $category->delete();
        return $this->successResponse(message: 'category deleted successfully');
    }
}
