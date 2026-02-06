<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    use ApiResponsesTrait;


    /**
     * list all user's addresses
     */

    public function index(Request $request)
    {
        $user = $request->user();
        $addresses = $user->addresses()->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(
            data: [
                'addresses' => $addresses
            ],
        );
    }


    /**
     * Add new address
     */

    public function store(Request $request)
    {

        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'apartment' => 'required|string|max:20',
            'floor' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'is_default' => 'boolean'
        ]);

        $userId = $request->user()->id;
        $validated['user_id'] = $userId;

        if ($request->is_default) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }
        $address = Address::create($validated);

        return $this->successResponse(
            message: "Address has been created successfully",
            data: [
                "address" => $address
            ],
            statusCode: 201

        );
    }


    /**
     * Update an address
     */

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'address' => 'sometimes|string|max:255',
            'apartment' => 'sometimes|string|max:20',
            'floor' => 'sometimes|string|max:20',
            'city' => 'sometimes|string|max:100',
            'is_default' => 'boolean'
        ]);
        $userId = $request->user()->id;

        if ($request->is_default) {
            Address::where('user_id', $userId)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }


        $address = Address::where('user_id', $userId)->findOrFail($id);

        $address->update($validated);

        return $this->successResponse(
            message: "Address has been updated successfully",
            data: [
                "address" => $address
            ]
        );
    }



    /**
     * Delete an address
     */

    public function destroy(Request $request, string $id)
    {
        $userId = $request->user()->id;
        $address = Address::where('user_id', $userId)->findOrFail($id);
        $address->delete();
        return $this->successResponse(
            message: "address deleted successfully"
        );
    }
}
