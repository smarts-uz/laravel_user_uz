<?php


namespace App\Services;


use App\Item\FaqsItem;
use App\Models\{FaqCategories, Faqs};
use Illuminate\Database\Eloquent\Collection;

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

}
