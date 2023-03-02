<?php


namespace App\Services\Task;


use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategories;
use Illuminate\Http\JsonResponse;
use TCG\Voyager\Models\Setting;

class FaqService
{
    public function index(): JsonResponse
    {
        $faqs = FaqCategories::query()->latest()->get();
        return response()->json([
            'success' => true,
            'data' => FaqCategoryResource::collection($faqs)
        ]);
    }

    public function get_all()
    {
        $setting = Setting::all();

        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }

    public function get_key($key)
    {
        $setting_key = Setting::query()->where('key', $key)->get();
        return response()->json([
            'success' => true,
            'data' => $setting_key ?? ''
        ]);
    }
}
