<?php


namespace App\Services\Task;


use App\Http\Resources\FaqResource;
use App\Models\FaqCategories;
use TCG\Voyager\Models\Setting;

class FaqService
{
    public function index()
    {
        $faqs = FaqCategories::query()->latest()->get();
        return response()->json([
            'success' => true,
            'data' => FaqResource::collection($faqs)
        ]);
    }

    public function get_all()
    {
        $setting = Setting::all();
        if (!empty($setting)) {
            $data = [
                'id' => $setting->id,
                'key' => $setting->key,
                'display_name' => $setting->display_name,
                'value' => $setting->value,
                'type' => $setting->type,
                'order' => $setting->order,
                'group' => $setting->group,
            ];
        } else {
            $data = [];
        }
        return response()->json([
            'success' => true,
            'data' => $data
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
