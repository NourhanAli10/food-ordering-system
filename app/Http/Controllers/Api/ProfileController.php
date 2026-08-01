<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponsesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponsesTrait;


    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return $this->successResponse(
            message: 'Profile fetched successfully',
            data: ['data' => $user]
        );
    }


    /**
     * Update user profile
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|min:2|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|string|max:12|unique:users,phone,' . $id,
        ]);

        $user = User::findOrfail($id);

        if (!$user) {
            return $this->errorResponse(message: 'user not found');
        }

        $user->update($validated);
        return $this->successResponse(
            message: 'Profile updated successfully',
            data: [
                'data' => $user
            ]
        );
    }

    /**
     * Change password
     */


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed'
        ]);
        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->errorResponse(
                message: "The provided credentials are incorrect"
            );
        }
        if (Hash::check($request->new_password, $user->password)) {
            return $this->errorResponse(message: "New password cannot be the same as your old password");
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        return $this->successResponse(
            message: 'Password changed successfully',
        );
    }


    /**
     * Delete account
     */
}
