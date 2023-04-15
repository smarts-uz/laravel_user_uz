<?php

namespace App\Http\Controllers\API;

use App\Services\Task\CategoriesAPIService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JetBrains\PhpStorm\ArrayShape;

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
     *     path="/api/popular-categories",
     *     tags={"CategoryAPI"},
     *     summary="Popular categories",
     *     description="https://t.me/c/1334612640/207",
     *     @OA\Parameter(
     *          in="query",
     *          description="Kategoriya nomi kiritiladi",
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
     *     description="https://t.me/c/1334612640/167",
     *     summary="Search Category by name",
     *     @OA\Parameter(
     *          in="query",
     *          description="Category name kiritiladi",
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
    #[ArrayShape(['data' => "array"])]
    public function search(Request $request): array
    {
        $parentId = (!empty($request->get('parent_id'))) ? $request->get('parent_id') : '';
        $name = $request->get('name');
        return $this->service->search($parentId, $name);
    }

    /**
     * @OA\Get(
     *     path="/api/categories-parent",
     *     tags={"CategoryAPI"},
     *     description="https://t.me/c/1334612640/168",
     *     summary="Get All Parent Categories",
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
     *     description="https://t.me/c/1334612640/169",
     *     summary="Get category by show ID",
     *     @OA\Parameter(
     *          in="path",
     *          description="Kategoriya id kiritiladi",
     *          name="id",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
    public function show($category_id): array
    {
        return $this->service->show($category_id);
    }

    /**
     * @OA\Get(
     *     path="/api/all-categories-childs",
     *     tags={"CategoryAPI"},
     *     description="https://t.me/c/1334612640/254",
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
    #[ArrayShape(['data' => "mixed"])]
    public function AllCategoriesChildsId(): array
    {
        return  $this->service->AllCategoriesChildsId();
    }
}
