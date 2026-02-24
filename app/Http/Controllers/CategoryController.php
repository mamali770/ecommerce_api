<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(2);

        return $this->responser([
            "categories" => CategoryResource::collection($categories),
            "links" => CategoryResource::collection($categories)->response()->getData()->links,
            "meta" => CategoryResource::collection($categories)->response()->getData()->meta
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "parent_id" => "nullable|exists:categories,id",
            "name" => "required",
            "description" => "nullable|string"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        $parentId = $request->parent_id === null ? 0 : $request->parent_id;

        $category = Category::create([
            "parent_id" => $parentId,
            "name" => $request->name,
            "description" => $request->description
        ]);

        return $this->responser(new CategoryResource($category), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->responser(new CategoryResource($category), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            "parent_id" => "nullable|exists:categories,id",
            "name" => "required",
            "description" => "nullable|string"
        ]);

        if ($validator->fails()) {
            return $this->responser(null, 422, $validator->messages());
        }

        $parentId = $request->parent_id === null ? 0 : $request->parent_id;

        $category->update([
            "parent_id" => $parentId,
            "name" => $request->name,
            "description" => $request->description
        ]);

        return $this->responser(new CategoryResource($category), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->responser(new CategoryResource($category), 200);
    }

    public function children(Category $category)
    {
        return $this->responser(new CategoryResource($category->load('children')));
    }

    public function parent(Category $category)
    {
        return $this->responser(new CategoryResource($category->load('parent')));
    }
}
