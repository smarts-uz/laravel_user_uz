<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\FaqCategories;

class FaqController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/faq",
     *     tags={"Faq"},
     *     summary="Faqs",
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
            'data' => FaqResource::collection(FaqCategories::query()->latest()->get())
        ]);
    }
}
