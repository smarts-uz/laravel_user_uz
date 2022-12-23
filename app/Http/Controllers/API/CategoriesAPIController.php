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
    public function index()
    {
        $categories = Category::query()->select('id', 'parent_id', 'name', 'ico')->withTranslation(app()->getLocale())->whereNull('parent_id')->orderBy("order", "asc")->get();
        return CategoryIndexResource::collection($categories);
    }

    /**
     * @OA\Get(
     *     path="/api/popular-categories",
     *     tags={"CategoryAPI"},
     *     summary="Popular categories",
     *     @OA\Parameter(
     *          in="query",
     *          name="category",
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
    public function popular(Request $request)
    {
        $name = $request->get('category');
        $categories = Category::query()->select('id', 'parent_id', 'name', 'ico')->withCount('tasks')->withTranslation('uz')
            ->whereTranslation('name', 'like', "%$name%")->orWhere('name', 'like', "%$name%")->whereNotNull('parent_id')->orderByDesc('tasks_count')->get();
        $response = [];
        foreach ($categories as $category) {
            $object['category_id'] = $category->id;
            $object['total'] = $category->tasks_count;
            $category->name = $category->getTranslatedAttribute('name', app()->getLocale() , 'ru');
            unset($category->translations);
            $object['category'] = $category;
            $response[] = $object;
        }
        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/category/search",
     *     tags={"CategoryAPI"},
     *     summary="Search Category by name",
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
        $parentId = $request->get('parent_id');
        $name = $request->get('name');
        $categories = Category::query()->whereNotNull('parent_id')->orderBy("order", "asc");
        if ($parentId)
            $categories
            ->where('parent_id', $parentId);
        if ($name)
            $categories->where('name','LIKE',"%$name%");
        return CategoryIndexResource::collection($categories->get());
    }

    /**
     * @OA\Get(
     *     path="/api/categories-parent",
     *     tags={"CategoryAPI"},
     *     summary="Get All Categories",
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
    public function parents(){
        $categories = Category::query()->whereNull('parent_id')->orderBy("order", "asc")->get();
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
        $category = $id->translate($request->get('lang'));
        return new CategoryShowResource($category);

    }
}
