<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogNewsResource;
use App\Models\BlogNew;

class BlogController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/blog-news",
     *     tags={"Blog News"},
     *     summary="Blogs",
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
     * )
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => BlogNewsResource::collection(BlogNew::query()->latest()->get())
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/blog-news/{blogNew}",
     *     tags={"Blog News"},
     *     summary="Blogs",
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
     * )
     */
    public function show(BlogNew $blogNew)
    {
        return response()->json([
            'success' => true,
            'data' => new BlogNewsResource($blogNew)
        ]);
    }
}
