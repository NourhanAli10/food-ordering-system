<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    use ApiResponsesTrait;

    public function index(City $city)
    {
        $areas = $city->areas;
        return $this->successResponse(data: ['areas' => $areas]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'city_id' => 'required|exists:cities,id',
        ]);

        $area = Area::create($validated);
        return $this->successResponse(
            message: 'Area created successfully',
            data: [
                'area' => $area
            ],
            statusCode: 201

        );
    }

    public function update(Request $request, string $id)
    {

        $area = Area::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'min:3',
                'max:255',
                Rule::unique('areas', 'name')->ignore($area->id),
            ],
            'delivery_fee' => 'sometimes|numeric|min:0',
            'city_id' => 'sometimes|exists:cities,id',
        ]);


        $area->update($validated);
        return $this->successResponse(
            message: 'Area updated successfully',
            data: [
                'area' => $area
            ]
        );
    }

    public function destroy(string $id)
    {
        $area = Area::findOrFail($id);
        $area->delete();
        return $this->successResponse(
            message: 'Area deleted successfully',

        );
    }
}
