<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\offerGroup;
use App\Services\Media;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminOfferController extends Controller
{

    use ApiResponsesTrait;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:offers,slug',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:active,inactive,expired',
            'start_date' => 'required|date_format:Y-m-d H:i',
            'expire_date' => "required|date_format:Y-m-d H:i",
            'type' => 'required|in:fixed,percentage,buy_x_get_y,combo',
            'value' => 'nullable|numeric|min:0',
            'buy_quantity' => 'nullable|numeric|min:1',
            'free_quantity' => 'nullable|numeric|min:1',
            'groups' => 'required|array|min:1',
            'groups.*.label' => 'required|string',
            'groups.*.max_choices' => 'required|integer|min:1',
            'groups.*.products'       => 'required|array|min:1',
            'groups.*.products.*.id' => 'required|exists:products,id',
            'groups.*.products.*.extra_price' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = $validated['slug']  ?? Str::slug($request->title);



        $offer = DB::transaction(function () use ($validated, $request) {

            if ($request->hasFile('image')) {
                $media = new Media;
                $newImage = $media->upload($request->file('image'), 'offers');
                $validated['image'] =  $newImage;
            }


            $offer = Offer::create(Arr::except($validated, ['groups']));


            foreach ($validated['groups'] as $group) {

                $offerGroup = $offer->groups()->create([
                    'label' => $group['label'],
                    'max_choices' => $group['max_choices']
                ]);


                foreach ($group['products'] as $product) {
                    $offerGroup->products()->attach(
                        $product['id'],
                        [
                            'extra_price' => $product['extra_price'] ?? 0
                        ]
                    );
                }
            }
            return $offer;
        });


        return $this->successResponse(
            message: "offer has been created successfully",
            data: [
                $offer
            ],
            statusCode: 201
        );
    }



    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:offers,slug' . $id,
            'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'sometimes|in:active,inactive,expired',
            'start_date' => 'sometimes|date_format:Y-m-d H:i',
            'expire_date' => "sometimes|date_format:Y-m-d H:i",
            'type' => 'sometimes|in:fixed,percentage,combo,buy_x_get_y',
            'value' => 'sometimes|numeric|min:0',
        ]);

        $offer = Offer::findOrFails($id);

        if ($request->slug) {
            $validated['slug'] = $request->slug;
        } elseif ($request->name) {
            $validated['slug'] = Str::slug($request->name);
        } else {
            $validated['slug'] = $offer->slug;
        }

        if ($request->hasFile('image')) {
            $media = new Media;
            $newImage = $media->upload($request->file('image'), 'offers', $offer->image);
            $validated['image'] =  $newImage;
        }
        $offer->update($validated);

        return $this->successResponse(
            message: "offer has been updated successfully",
            data: [
                $offer
            ]
        );
    }




    public function destroy(string $id)
    {
        $offer = Offer::findOrFails($id);
        if ($offer->image) {
            $media = new Media;
            $media->delete($offer->image, 'offers');
        }
        $offer->delete();
        return $this->successResponse(message: 'offer deleted successfully');
    }
}
