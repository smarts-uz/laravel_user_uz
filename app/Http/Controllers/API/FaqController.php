<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Http\Resources\SettingResource;
use App\Models\FaqCategories;
use TCG\Voyager\Models\Setting;

class FaqController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/faq",
     *     tags={"FAQ"},
     *     summary="faqs",
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

    /**
     * @OA\Get(
     *     path="/api/settings/get-all",
     *     tags={"Setting"},
     *     summary="setting all",
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
    public function get_all()
    {
        return response()->json([
            'success' => true,
            'data' => SettingResource::collection(Setting::all())
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/settings/get-all/{key}",
     *     tags={"Setting"},
     *     summary="setting key",
     *     @OA\Parameter (
     *          in="path",
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
    public function get_key($key)
    {
        $setting_key = Setting::query()->where('key',$key)->get();
        return response()->json([
            'success' => true,
            'data' => $setting_key
        ]);
    }

}
