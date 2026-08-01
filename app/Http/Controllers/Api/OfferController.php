<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    use ApiResponsesTrait;

    public function index() {
        $offers = Offer::where('status', 'active')->orderBy('created_at', 'DESC')->get();
        return $this->successResponse(
            data: ['offers' => $offers]
        );
    }


    public function show(string $id) {
        $offer = Offer::with('groups.products')->findOrFail($id);
        return $this->successResponse(
            data: ['offer' => $offer]
        );
    }


}
