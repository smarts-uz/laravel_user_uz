<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CategoryIndexResource;
use App\Http\Resources\CategoryShowResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoriesAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"Category"},
     *     summary="Get list of Category",
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *     )
     * )
     */
    public function index(Request $request)
    {
        $categories = Category::select('parent_id,name,ico')->withTranslation($request->lang)->whereNull('parent_id')->get();
        return CategoryIndexResource::collection($categories);
    }
    public function search(Request $request)
    {
        $parentId = $request->parent_id;
        $name = $request->name;
        $categories = Category::query()->whereNotNull('parent_id');
        if ($parentId)
            $categories
            ->where('parent_id', $parentId);
        if ($name)
            $categories->where('name','LIKE',"%$name%");
        return CategoryIndexResource::collection($categories->get());
    }

    public function parents(Request $request){
        $categories = Category::query()->whereNull('parent_id')->get();
        return CategoryIndexResource::collection($categories);

    }



    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Category"},
     *     summary="Get list of Category",
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *     ),
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     * )
     */
    public function show(Category $id, Request $request){
        $category = $id->translate($request->lang);
        return new CategoryShowResource($category);

    }
}
