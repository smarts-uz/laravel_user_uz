<?php


namespace App\Services;


use App\Item\FaqsItem;
use App\Models\FaqCategories;
use App\Models\Faqs;

class FaqsService
{

    public function questions($id){
        $item = new FaqsItem();

        $item-> fq = Faqs::withTranslations(['ru', 'uz'])->where('category_id', $id)->get();
        $item-> fc = FaqCategories::withTranslations(['ru', 'uz'])->where('id', $id)->first();
        return $item;
    }

}
