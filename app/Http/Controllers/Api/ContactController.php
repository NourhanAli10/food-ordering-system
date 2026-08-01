<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponsesTrait;


    public function send(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|min:2|max:255',
            'message' => 'required|string|min:10',
        ]);

        Contact::create($validated);

        Mail::To('nouranonamm@gmail.com');

        return $this->successResponse(message: "email sent successfully");

    }
}
