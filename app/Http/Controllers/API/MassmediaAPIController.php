<?php

namespace App\Http\Controllers\API;

use App\Models\Massmedia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MassmediaAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/press",
     *     tags={"MassmediaAPI"},
     *     summary="Get list of Massmedia",
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
     *
     */
    public function index()
    {
        $medias = Massmedia::paginate(20);

        if($medias){
            return response()->json($medias);
        }
        return response()->json('Малумот йок');
        
    }
}
