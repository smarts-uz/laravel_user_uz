<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioRequest;
use App\Http\Resources\PortfolioIndexResource;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Routing\Controller;


class PortfolioAPIController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/portfolio_albums/{performer}",
     *     tags={"PortfolioAPI"},
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

        return PortfolioIndexResource::collection($performer->portfolios);
    }


    /**
     * @OA\Get(
     *     path="/api/portfolio_album/{portfolio}",
     *     tags={"PortfolioAPI"},
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

        return new PortfolioIndexResource($portfolio);
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


    /**
     * @OA\Post(
     *     path="/api/profile/portfolio/create",
     *     tags={"PorfolioAPI"},
     *     summary="Create portfolio",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="comment",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="description",
     *                    type="string",
     *                 ),
     *             ), 
     *         ),
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
    public function createPortfolio(PortfolioRequest $request)
    {
        $data = $request->validated();
        $portfolio = Portfolio::create($data);
        return new PortfolioIndexResource($portfolio);
    }


}
