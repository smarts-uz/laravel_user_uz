<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\FaqCategories;
use App\Services\Task\FaqService;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{

    public FaqService $service;

    public function __construct(FaqService $faqService)
    {
        $this->service = $faqService;
    }

    /**
     * @OA\Get(
     *     path="/api/faq",
     *     tags={"FAQ"},
     *     description="[**Telegram :** https://t.me/c/1334612640/255](https://t.me/c/1334612640/255).",
     *     summary="Tez-tez beriladigan savollar ro'yxatini olish",
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
        return $this->service->index();
    }

    /**
     * @OA\Get(
     *     path="/api/faq/{faqId}",
     *     tags={"FAQ"},
     *     description="[**Telegram :** https://t.me/c/1334612640/253](https://t.me/c/1334612640/253).",
     *     summary="Get faq by show ID",
     *     @OA\Parameter (
     *          in="path",
     *          required=true,
     *          description="Tez-tez beriladigan savollar idsi kiritiladi",
     *          name="faqId",
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
    public function faq(FaqCategories $faqId): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new FaqResource($faqId)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/settings/get-all",
     *     tags={"Setting"},
     *     description="[**Telegram :** https://t.me/c/1334612640/160](https://t.me/c/1334612640/160).",
     *     summary="Get list of setting",
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
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function get_all(): JsonResponse
    {
        return $this->service->get_all();
    }

    /**
     * @OA\Get(
     *     path="/api/settings/{settingKey}",
     *     tags={"Setting"},
     *     description="[**Telegram :** https://t.me/c/1334612640/163](https://t.me/c/1334612640/163).",
     *     summary="Get setting by show key",
     *     @OA\Parameter (
     *          in="path",
     *          required=true,
     *          description="Adminkadan yaratilgan kerakli settingning keyi kiritiladi",
     *          name="settingKey",
     *          @OA\Schema (
     *              type="string"
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
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function get_key($settingKey): JsonResponse
    {
        return $this->service->get_key($settingKey);
    }

}
