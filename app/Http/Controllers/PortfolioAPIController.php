<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;


class PortfolioAPIController extends Controller
{
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
