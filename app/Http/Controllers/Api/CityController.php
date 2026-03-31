<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    use ApiResponsesTrait;


    public function index()
    {
        $citites = City::all();
        return $this->successResponse(data: ['cities' => $citites]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255|unique:cities,name',
        ]);

        $city = City::create($validated);
        return $this->successResponse(
            message: 'city created successfully',
            data: [
                $city
            ],
            statusCode: 201

        );
    }


    public function update(Request $request, string $id)
    {

        $city = City::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('cities', 'name')->ignore($city->id)],
        ]);


        $city->update($validated);

        return $this->successResponse(
            message: 'city updated successfully',
            data: [
                $city
            ]

        );
    }


    public function destroy(string $id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        return $this->successResponse(
            message: 'city deleted successfully',

        );
    }
}
