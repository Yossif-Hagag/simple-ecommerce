<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    use ApiResponseTrait;

    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:credit_card,cash_on_delivery',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(data: null, status: Response::HTTP_UNPROCESSABLE_ENTITY, message: $validator->errors());
        }

        $user = auth()->user();
        $cart = $user->carts()->first();
        if (!$cart || $cart->products()->count() == 0) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "No Items In The Cart To Checkout");
        }

        $totalPrice = 0;
        foreach ($cart->products as $product) {
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => Order::STATUS_PENDING,
            'name' => $request->name,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
        ]);

        foreach ($cart->products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $product->pivot->quantity,
                'price' => $product->price,
            ]);
        }

        $cart->products()->detach();

        $order->complete();

        return $this->apiResponse(data: $order->load('orderItems.product'), status: Response::HTTP_OK, message: "Order Created Successfully");
    }

    public function cancelOrder(Order $order)
    {
        if ($order->status == Order::STATUS_COMPLETED) {
            return $this->apiResponse(data: null, status: Response::HTTP_FORBIDDEN, message: "Cannot cancel a completed order");
        }

        $order->cancel();

        return $this->apiResponse(data: $order, status: Response::HTTP_OK, message: "Order Canceled Successfully");
    }
}
