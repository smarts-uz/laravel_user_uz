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
     *     summary="Get list of Faqs",
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
     *     path="/api/faq/{faq_id}",
     *     tags={"FAQ"},
     *     summary="Get faq by show ID",
     *     @OA\Parameter (
     *          in="path",
     *          description="faq id kiritiladi",
     *          name="faq_id",
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
    public function faq(FaqCategories $faq_id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new FaqResource($faq_id)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/settings/get-all",
     *     tags={"Setting"},
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
     *     path="/api/settings/{key}",
     *     tags={"Setting"},
     *     summary="Get setting by show key",
     *     @OA\Parameter (
     *          in="path",
     *          description="Adminkadan yaratilgan kerakli settingning keyi kiritiladi",
     *          name="key",
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
    public function get_key($key): JsonResponse
    {
        return $this->service->get_key($key);
    }

}
