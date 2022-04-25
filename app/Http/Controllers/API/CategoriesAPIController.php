<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryIndexResource;
use App\Http\Resources\CategoryShowResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/categories",
     *     tags={"CategoryAPI"},
     *     summary="Get list of Category",
     *     @OA\Parameter(
     *          in="query",
     *          name="lang",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $categories = Category::withTranslation($request->lang)->get();
        return CategoryIndexResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/category/search",
     *     tags={"CategoryAPI"},
     *     summary="Get list of Category",
     *     @OA\Parameter(
     *          in="query",
     *          name="name",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $categories = Category::query()->whereNotNull('parent_id')->where('name','LIKE',"%$request->name%")->get();
        return CategoryIndexResource::collection($categories);
    }




    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"CategoryAPI"},
     *     summary="Get category by show ID",
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     */
    public function show(Category $id, Request $request){
        $category = $id->translate($request->lang);
        return new CategoryShowResource($category);

    }
}
