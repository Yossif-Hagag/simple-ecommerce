<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function profile()
    {
        /** @var User */
        $authUser = auth()->user();

        return $this->apiResponse(
            data: $authUser,
            status: Response::HTTP_OK
        );
    }
}
