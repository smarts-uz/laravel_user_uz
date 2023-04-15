<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogNewsResource;
use App\Models\BlogNew;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/blog-news",
     *     tags={"Blog News"},
     *     description="https://t.me/c/1334612640/164",
     *     summary="Barcha yangiliklarni olish uchun api",
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
     *     ),
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => BlogNewsResource::collection(BlogNew::query()->latest()->get())
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/blog-news/{newsId}",
     *     tags={"Blog News"},
     *     description="https://t.me/c/1334612640/165",
     *     summary="Yangilikni kiritilgan idga qarab olish",
     *     @OA\Parameter (
     *          in="path",
     *          description="Kerakli yangilik idsi kiritiladi",
     *          name="newsId",
     *          required=true,
     *          @OA\Schema (
     *              type="integer"
     *          )
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
     *     ),
     * )
     */
    public function show(BlogNew $newsId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new BlogNewsResource($newsId)
        ]);
    }
}
