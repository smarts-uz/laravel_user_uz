<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogNewsResource;
use App\Models\BlogNew;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => BlogNewsResource::collection(BlogNew::all())
        ]);
    }
}
