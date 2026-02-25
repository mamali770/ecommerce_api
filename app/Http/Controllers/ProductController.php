<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate(2);

        return $this->responser([
            "products" => ProductResource::collection($products->load('images')),
            "links" => ProductResource::collection($products)->response()->getData()->links,
            "meta" => ProductResource::collection($products)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "brand_id" => "required|exists:brands,id",
            "category_id" => "required|exists:categories,id",
            "primary_image" => "required|image",
            "description" => "required|string",
            "price" => "required|integer",
            "quantity" => "required|integer",
            "delivery_amount" => "required|integer",
            "images.*" => "nullable|image"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        DB::beginTransaction();

        $primary_image = saveImage($request->primary_image, "images/products/" . now()->year . '/' . now()->format('m'));

        if ($request->has('images')) {
            $fileNameImages = [];
            foreach ($request->images as $image) {
                $fileNameImage = saveImage($image, "images/products/" . now()->year . '/' . now()->format('m'));
                array_push($fileNameImages, $fileNameImage);
            }
        }

        $product = Product::create([
            "name" => $request->name,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "primary_image" => $primary_image,
            "description" => $request->description,
            "price" => $request->price,
            "quantity" => $request->quantity,
            "delivery_amount" => $request->delivery_amount,
        ]);

        if ($request->has('images')) {
            foreach ($fileNameImages as $image) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "name" => $image
                ]);
            }
        }

        DB::commit();

        return $this->responser(new ProductResource($product->load('images')), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->responser(new ProductResource($product->load('images')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "brand_id" => "required|exists:brands,id",
            "category_id" => "required|exists:categories,id",
            "primary_image" => "nullable|image",
            "description" => "required|string",
            "price" => "required|integer",
            "quantity" => "required|integer",
            "delivery_amount" => "required|integer",
            "images.*" => "nullable|image"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        DB::beginTransaction();

        if ($request->has('primary_image')) {
            $primary_image = saveImage($request->primary_image, "images/products/" . now()->year . '/' . now()->format('m'));
        }

        if ($request->has('images')) {
            $fileNameImages = [];
            foreach ($request->images as $image) {
                $fileNameImage = saveImage($image, "images/products/" . now()->year . '/' . now()->format('m'));
                array_push($fileNameImages, $fileNameImage);
            }
        }

        $product->update([
            "name" => $request->name,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "primary_image" => $request->has('primary_image') ? $primary_image : $product->primary_image,
            "description" => $request->description,
            "price" => $request->price,
            "quantity" => $request->quantity,
            "delivery_amount" => $request->delivery_amount,
        ]);

        if ($request->has('images')) {
            foreach ($product->images as $productImage) {
                $productImage->delete();
            }
            foreach ($fileNameImages as $image) {
                ProductImage::create([
                    "product_id" => $product->id,
                    "name" => $image
                ]);
            }
        }

        DB::commit();

        return $this->responser(new ProductResource($product->load('images')), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->responser(new ProductResource($product));
    }
}
