<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartsController extends Controller
{
    use ApiResponseTrait;

    public function addProductToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
        }

        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->apiResponse(null, Response::HTTP_NOT_FOUND, 'Product Not Found');
        }

        if ($product->quantity < 1) {
            return $this->apiResponse(null, Response::HTTP_BAD_REQUEST, 'Product Out Of Stock');
        }

        $reservedInOtherCarts = DB::table('cart_product')
            ->where('product_id', $product->id)
            ->where('cart_id', '!=', $cart->id)
            ->sum('quantity');

        $existingProduct = $cart->products()->where('product_id', $product->id)->first();
        $quantityInCart = $existingProduct ? $existingProduct->pivot->quantity : 0;

        $totalQuantity = $quantityInCart + $request->quantity;

        if ($reservedInOtherCarts + $totalQuantity > $product->quantity) {
            return $this->apiResponse(null, Response::HTTP_BAD_REQUEST, 'Insufficient stock available across carts');
        }

        if ($existingProduct) {
            $cart->products()->updateExistingPivot($product->id, ['quantity' => $totalQuantity]);
        } else {
            $cart->products()->attach($product->id, ['quantity' => $request->quantity]);
        }

        return $this->apiResponse($cart->load('products'), Response::HTTP_OK, "Product Added To Cart Successfully");
    }
    public function clearProductFromCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(data: null, status: Response::HTTP_UNPROCESSABLE_ENTITY, message: $validator->errors()->first());
        }

        $user = auth()->user();
        $cart = $user->carts()->first();
        if (!$cart) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Cart Not Found");
        }

        $product = Product::find($request->product_id);
        $cartProduct = $cart->products()->where('product_id', $request->product_id)->first();
        if (!$cartProduct) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Product Not Found In The Cart");
        }

        $cart->products()->detach($product->id);
        return $this->apiResponse(data: null, status: Response::HTTP_OK, message: "Product Removed From Cart Successfully");
    }
    public function updateProductInCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY, $validator->errors()->first());
        }

        $user = auth()->user();
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $product = Product::find($request->product_id);

        if (!$product) {
            return $this->apiResponse(null, Response::HTTP_NOT_FOUND, 'Product Not Found');
        }

        if ($product->quantity < 1) {
            return $this->apiResponse(null, Response::HTTP_BAD_REQUEST, 'Product Out Of Stock');
        }

        $reservedInOtherCarts = DB::table('cart_product')
            ->where('product_id', $product->id)
            ->where('cart_id', '!=', $cart->id)
            ->sum('quantity');

        if ($request->quantity + $reservedInOtherCarts > $product->quantity) {
            return $this->apiResponse(null, Response::HTTP_BAD_REQUEST, 'Insufficient stock available across carts');
        }

        $cart->products()->syncWithoutDetaching([
            $product->id => ['quantity' => $request->quantity]
        ]);

        return $this->apiResponse($cart->load('products'), Response::HTTP_OK, 'Product Quantity Updated Successfully');
    }
    public function getCart()
    {
        $user = auth()->user();
        $cart = $user->carts()->first();
        if (!$cart) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Cart Not Found Or Empty");
        }
        return $this->apiResponse(data: $cart->load('products'), status: Response::HTTP_OK, message: "Cart Retrieved Successfully");
    }
    public function emptyCart()
    {
        $user = auth()->user();
        $cart = $user->carts()->first();
        if (!$cart) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Cart Not Found");
        }
        $cart->products()->detach();
        return $this->apiResponse(data: null, status: Response::HTTP_OK, message: "Cart Empty Successfully");
    }
}
