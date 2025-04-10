<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class WishlistsController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $user = auth()->user();
        $wishlist = $user->wishlists()->with('products')->first();

        if (!$wishlist) {
            return $this->apiResponse([], Response::HTTP_OK, "No Wishlist Yet");
        }

        return $this->apiResponse($wishlist->products, Response::HTTP_OK, "Wishlist Retrieved Successfully");
    }

    public function addProductToWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
        }

        $user = auth()->user();
        $wishlist = $user->wishlists()->firstOrCreate([]);

        $wishlist->products()->syncWithoutDetaching([$request->product_id]);

        return $this->apiResponse($wishlist->load('products'), Response::HTTP_OK, "Product Added To Wishlist Successfully");
    }

    public function removeProductFromWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
        }

        $user = auth()->user();
        $wishlist = $user->wishlists()->first();

        if (!$wishlist) {
            return $this->apiResponse(null, Response::HTTP_NOT_FOUND, "Wishlist Not Found");
        }

        $wishlist->products()->detach($request->product_id);

        return $this->apiResponse($wishlist->load('products'), Response::HTTP_OK, "Product Removed From Wishlist Successfully");
    }
}
