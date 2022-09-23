<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FaqCategories;
use App\Services\FaqsService;
use Illuminate\Routing\Controller;

class FaqsController extends Controller
{
    public function index(Request $request)
    {
        $fc = FaqCategories::all();

        if ($request->input('search')) {
            $fc = FaqCategories::query()->where('title','like', '%'.$request->input('search')."%")->get();
        }
        return view('faq.faq', compact('fc'));
    }

    public function questions($id)
    {
        $service = new FaqsService();
        $item = $service->questions($id);
        return view('faq.faq-ans',[
            'fq' => $item->fq,
            'fc' => $item->fc,
        ]);
    }
}
