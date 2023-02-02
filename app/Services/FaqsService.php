<?php


namespace App\Services;


use App\Item\FaqsItem;
use App\Models\FaqCategories;
use App\Models\Faqs;
use Illuminate\Database\Eloquent\Collection;

class FaqsService
{
    /**
     *
     * Function  child_report
     * Mazkur metod faqni kategoriyalar bo'yicha chiqarib beradi
     * @param int $id
     *
     */
    public function questions(int $id): FaqsItem
    {
        $item = new FaqsItem();

        $item-> fq = Faqs::query()->where('category_id', $id)->get();
        $item-> fc = FaqCategories::query()->where('id', $id)->first();
        return $item;
    }

    public static function getFaqCategories(): Collection
    {
        return FaqCategories::all();
    }

}
