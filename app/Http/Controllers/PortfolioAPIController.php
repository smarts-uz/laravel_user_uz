<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioRequest;
use App\Http\Resources\PortfolioIndexResource;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Routing\Controller;


class PortfolioAPIController extends Controller
{



    public function index(User $performer){

        return PortfolioIndexResource::collection($performer->portfolios);
    }
    public function show(Portfolio $portfolio){

        return new PortfolioIndexResource($portfolio);
    }


    public function delete(Portfolio $portfolio){
        portfolioGuard($portfolio);

        $portfolio->delete();

        return response()->json(["success" => true]);
    }

    public function createPortfolio(PortfolioRequest $request)
    {
        $data = $request->validated();
        $portfolio = Portfolio::create($data);
        return new PortfolioIndexResource($portfolio);
    }


}
