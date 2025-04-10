<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    use ApiResponseTrait;

    public function products()
    {
        $products = Product::paginate('12');
        if ($products) {
            return $this->apiResponse(data: $products, status: Response::HTTP_OK, message: "All Products");
        }
        return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Product Not Found");
    }

    public function read(string $id)
    {
        $product = Product::find($id);
        if ($product) {
            return $this->apiResponse(data: $product, status: Response::HTTP_OK, message: "Read Product");
        }
        return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: "Product Not Found");
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(data: null, status: Response::HTTP_UNPROCESSABLE_ENTITY, message: $validator->errors()->first());
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('product_images', 'public');
        } else {
            return $this->apiResponse(data: null, status: Response::HTTP_UNPROCESSABLE_ENTITY, message: 'The image field must be a valid image');
        }

        $product = Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'quantity' => $request->input('quantity'),
            'sku' => $request->input('sku'),
            'category_id' => $request->input('category_id'),
            'image' => $imagePath,
        ]);

        return $this->apiResponse(data: $product, status: Response::HTTP_CREATED, message: "Product Created Successfully");
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: 'Product Not Found');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:products,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'description' => 'string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(data: null, status: Response::HTTP_UNPROCESSABLE_ENTITY, message: $validator->errors()->first());
        }

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('product_images', 'public');
            $product->image = $imagePath;
        }

        $product->update($request->only(['name', 'description', 'price', 'quantity', 'sku', 'category_id']));

        return $this->apiResponse(data: $product, status: Response::HTTP_OK, message: "Product Updated Successfully");
    }

    public function delete(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->apiResponse(data: null, status: Response::HTTP_NOT_FOUND, message: 'Product Not Found');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return $this->apiResponse(data: $product, status: Response::HTTP_OK, message: "Product Deleted Successfully");
    }
}
