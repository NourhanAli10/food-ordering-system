<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponsesTrait;

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:12|unique:users,phone',
            'role' => 'nullable|in:customer,admin',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create($validated);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            message: 'user has been registered successfully',
            data: [
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token,
            ],
            statusCode: 201
        );
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string',
        ]);


        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->errorResponse(message: ' The provided credentials are incorrect.', statusCode: 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            message: 'you have login successfully',
            data: [
                'user' => $user,
                'token_type' => "Bearer",
                'access_token' => $token
            ],
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(message: 'you have logout successfully');
    }
}
