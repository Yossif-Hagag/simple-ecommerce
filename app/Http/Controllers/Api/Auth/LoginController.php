<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:191'],
            'password' => ['required', 'max:191'],
        ]);

        if (!auth()->attempt($data)) {
            return $this->apiResponse(status: Response::HTTP_NOT_FOUND, message: 'Email or Password Not Found');
        }

        /** @var User */
        $authUser = auth()->user();

        return $this->apiResponse(
            data: [
                'name' => $authUser->name,
                'token' => $authUser->createToken("{$authUser->id}_{$authUser->email}")->plainTextToken,
            ],
            status: Response::HTTP_OK
        );
    }
}
