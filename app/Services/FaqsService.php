<?php


namespace App\Services;


use App\Item\FaqsItem;
use App\Models\{BlogNew, FaqCategories, Faqs};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class FaqsService
{
    /**
     *
     * Function  child_report
     * Mazkur metod faqni kategoriyalar bo'yicha chiqarib beradi
     * @param int $id
     * @return FaqsItem
     */
    public function questions(int $id): FaqsItem
    {
        $item = new FaqsItem();
        $item-> fq = Faqs::query()->where('category_id', $id)->get();
        $item-> fc = FaqCategories::query()->where('id', $id)->first();
        return $item;
    }


    /**
     * bu method faqcategories tabledagi barcha qiymatni qaytaradi
     * @return Collection
     */
    public static function getFaqCategories(): Collection
    {
        return FaqCategories::all();
    }

    /**
     * bu method barcha yangiliklarni qaytaradi
     * @return JsonResponse
     */
    public function blog_news_index(): JsonResponse
    {
        $blog_news = BlogNew::query()->latest()->get();
        $data = [];
        foreach ($blog_news as $blog_new){
            $data[] = [
                'id' => $blog_new->id,
                'title' => $blog_new->getTranslatedAttribute('title'),
                'text' =>  $blog_new->getTranslatedAttribute('text'),
                'desc' => $blog_new->getTranslatedAttribute('desc'),
                'img' => asset('storage/'. $blog_new->img),
                'created_at' => $blog_new->created_at
            ];
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Bu method $newsId bo'yicha kerakli qiymatni qaytaradi
     * @param $newsId
     * @return JsonResponse
     */
    public function blog_news_show($newsId): JsonResponse
    {
        $blog_new = BlogNew::find($newsId);
        $data = !empty($blog_new) ? [
            'id' => $blog_new->id,
            'title' => $blog_new->getTranslatedAttribute('title'),
            'text' =>  $blog_new->getTranslatedAttribute('text'),
            'desc' => $blog_new->getTranslatedAttribute('desc'),
            'img' => asset('storage/'. $blog_new->img),
            'created_at' => $blog_new->created_at
        ]: [];
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


}
