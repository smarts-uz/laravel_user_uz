<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\CategoryIndexResource;
use App\Http\Resources\CategoryShowResource;
use App\Models\Category;
use App\Services\Task\CategoriesAPIService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CategoriesAPIController extends Controller
{
    /**
     * @var CategoriesAPIService
     */
    public CategoriesAPIService $service;

    public function __construct(CategoriesAPIService $categoriesAPIService)
    {
        $this->service = $categoriesAPIService;
    }

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
        return $this->service->index();
    }

    /**
     * @OA\Get(
     *     path="/api/popular-categories",
     *     tags={"CategoryAPI"},
     *     summary="Popular categories",
     *     @OA\Parameter(
     *          in="query",
     *          name="category",
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
    public function popular(Request $request): array
    {
        $name = $request->get('category');
        return $this->service->popular($name);
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
        $parentId = (!empty($request->get('parent_id'))) ? $request->get('parent_id') : '';
        $name = $request->get('name');
        return $this->service->search($parentId, $name);
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
    public function parents(): array
    {
        return $this->service->parents();
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
    public function show($category_id, Request $request)
    {
        $lang = $request->get('lang');
        return $this->service->show($category_id, $lang);
    }

    /**
     * @OA\Get(
     *     path="/api/all-categories-childs",
     *     tags={"CategoryAPI"},
     *     summary="All Categories Childs Id",
     *     security={
     *         {"token": {}}
     *     },
     *     @OA\Response(
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
    public function AllCategoriesChildsId(): array
    {
        return  $this->service->AllCategoriesChildsId();
    }
}
