<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\FaqsService;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    protected FaqsService $faqService;

    public function __construct()
    {
        $this->faqService = new FaqsService();
    }
    /**
     * @OA\Get(
     *     path="/api/blog-news",
     *     tags={"Blog News"},
     *     description="[**Telegram :** https://t.me/c/1334612640/164](https://t.me/c/1334612640/164).",
     *     summary="Barcha yangiliklarni olish uchun api",
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation",
     *       @OA\JsonContent(
     *          type="object",
     *          @OA\Property(
     *             property="data"
     *          ),
     *       )
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
    public function index(): JsonResponse
    {
       return $this->faqService->blog_news_index();
    }

    /**
     * @OA\Get(
     *     path="/api/blog-news/{newsId}",
     *     tags={"Blog News"},
     *     description="[**Telegram :** https://t.me/c/1334612640/165](https://t.me/c/1334612640/165).",
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
    public function show($newsId): JsonResponse
    {
       return $this->faqService->blog_news_show($newsId);
    }
}
