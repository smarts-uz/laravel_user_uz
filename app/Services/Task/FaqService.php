<?php


namespace App\Services\Task;


use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategories;
use Illuminate\Http\JsonResponse;
use TCG\Voyager\Models\Setting;

class FaqService
{
    /**
     * faq category qiymatlarni qaytaradi
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $faqs = FaqCategories::query()->latest()->get();
        return response()->json([
            'success' => true,
            'data' => FaqCategoryResource::collection($faqs)
        ]);
    }

    /**
     * setting tabledan hamma qiymatni qaytaradi
     * @return JsonResponse
     */
    public function get_all(): JsonResponse
    {
        $setting = Setting::all();

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    /**
     * Setting table dan $key bo'yicha qiymatarni qaytaradi
     * @param $key
     * @return JsonResponse
     */
    public function get_key($key): JsonResponse
    {
        $setting_key = Setting::query()->where('key', $key)->get();
        return response()->json([
            'success' => true,
            'data' => $setting_key ?? ''
        ]);
    }
}
