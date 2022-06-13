<?php


namespace App\Services;


use App\Item\FaqsItem;
use App\Models\FaqCategories;
use App\Models\Faqs;

class FaqsService
{
    /**
     *
     * Function  child_report
     * Mazkur metod faqni kategoriyalar bo'yicha chiqarib beradi
     * @param $id  Object
     *
     */
    public function questions($id){
        $item = new FaqsItem();

        $item-> fq = Faqs::withTranslations(['ru', 'uz'])->where('category_id', $id)->get();
        $item-> fc = FaqCategories::withTranslations(['ru', 'uz'])->where('id', $id)->first();
        return $item;
    }

}
