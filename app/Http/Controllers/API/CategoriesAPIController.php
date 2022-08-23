<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CategoryIndexResource;
use App\Http\Resources\CategoryShowResource;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
        $categories = Category::select('parent_id', 'name', 'ico')->withTranslation(app()->getLocale())->whereNull('parent_id')->get();
        return CategoryIndexResource::collection($categories);
    }

    public function popular(Request $request)
    {
        $name = $request->get('category');
        return Task::query()->with('category:id,name,parent_id,ico')
            ->whereHas('category', function ($q) use ($name){
                return $q->where('name', 'like', "%$name%");
            })
            ->select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();
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
    public function parents(Request $request){
        $categories = Category::query()->whereNull('parent_id')->get();
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
