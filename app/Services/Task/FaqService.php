<?php


namespace App\Services\Task;

use App\Models\FaqCategories;
use Illuminate\Http\JsonResponse;
use TCG\Voyager\Models\Setting;

class FaqService
{
    /**
     * faq category qiymatlarni qaytaradi
     * @return array
     */
    public function index(): array
    {
        $faqs = FaqCategories::query()->latest()->get();
        $data = [];
        foreach ($faqs as $faq) {
            $data[] = [
                'id' => $faq->id,
                'title' => $faq->getTranslatedAttribute('title'),
            ];
        }
       return $data;
    }

    /**
     * @param $faqId
     * @return array
     */
    public function faqAll($faqId): array
    {
        $faq = FaqCategories::find($faqId);
        return !empty($faq) ? [
            'id' => $faq->id,
            'title' => $faq->getTranslatedAttribute('title'),
            'description' => $faq->getTranslatedAttribute('description'),
            'logo' => asset('storage/'.$faq->logo),
        ]: [];
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
