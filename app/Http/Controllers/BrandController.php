<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(2);

        // return BrandResource::collection($brands)->response();

        return $this->responser([
            "brands" => BrandResource::collection($brands),
            "links" => BrandResource::collection($brands)->response()->getData()->links,
            "meta" => BrandResource::collection($brands)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "display_name" => "required|unique:brands"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        $brand = Brand::create([
            "name" => $request->name,
            "display_name" => $request->display_name
        ]);

        return $this->responser(new BrandResource($brand), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return $this->responser(new BrandResource($brand));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "display_name" => "required|unique:brands"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        $brand->update([
            "name" => $request->name,
            "display_name" => $request->display_name
        ]);

        return $this->responser(new BrandResource($brand));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return $this->responser(new BrandResource($brand));
    }
}
