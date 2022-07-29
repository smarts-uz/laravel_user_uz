<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioRequest;
use App\Http\Resources\PortfolioIndexResource;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;


class PortfolioAPIController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/portfolio_albums/{performer}",
     *     tags={"Portfolio"},
     *     summary="Get Portfolios By Performer ID",
     *     @OA\Parameter (
     *          in="path",
     *          name="performer",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     */
    public function index(User $performer){

        return response()->json(['success' => true,'data'=>PortfolioIndexResource::collection($performer->portfolios)]);
    }


    /**
     * @OA\Get(
     *     path="/api/portfolio_album/{portfolio}",
     *     tags={"Portfolio"},
     *     summary="Get Portfolio By Portfolio ID",
     *     @OA\Parameter (
     *          in="path",
     *          name="portfolio",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     )
     * )
     */
    public function show(Portfolio $portfolio){

        return  response()->json(['success' => true,'data'=> new PortfolioIndexResource($portfolio)]);
    }


    /**
     * @OA\DELETE(
     *     path="/api/profile/delete/portfolio/{portfolio}",
     *     tags={"PorfolioAPI"},
     *     summary="Delete portfolio",
     *     @OA\Parameter (
     *          in="path",
     *          name="portfolio",
     *          required=true,
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Response (
     *          response=200,
     *          description="Successful operation"
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
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


    public function deleteImage(Request $request, Portfolio $portfolio)
    {
        $image = $request->get('image');
        File::delete(public_path() . '/portfolio/'. $image);
        $images = json_decode($portfolio->image);
        $updatedImages = array_diff($images, [$image]);
        $portfolio->image = json_encode(array_values($updatedImages));
        $portfolio->save();
        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted'
        ]);
    }


}
