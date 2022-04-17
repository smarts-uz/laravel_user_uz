<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PerformerRegisterRequest;
use App\Http\Resources\PerformerIndexResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function Symfony\Component\String\s;

class PerformerAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/performers",
     *     tags={"Performers"},
     *     summary="Get list of Performers",
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     )
     * )
     *
     */
    public function service()
    {
        $performers = User::where('role_id', 2)->get();
        return PerformerIndexResource::collection($performers);
    }

    public function online_performers()
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $performers = User::where('role_id', 2)->where('last_seen', ">=",$date)->get();
        return PerformerIndexResource::collection($performers);
    }

    /**
     * @OA\Get(
     *     path="/api/performers/{performer}",
     *     tags={"Performers"},
     *     summary="Get list of Performers",
     *     @OA\Parameter(
     *          in="path",
     *          name="performer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     )
     * )
     *
     */
    public function performer(User $performer)
    {
        setView($performer);

        return $performer->role_id == 5 ? new PerformerIndexResource($performer) : abort(404);
    }

    public function register(PerformerRegisterRequest $request)
    {

    }

    public function validatorRules($step)
    {
        if ($step == 1) {
            return [
                'name' => 'required',
                'address' => 'required',
                'birth_date' => 'required'
            ];
        } elseif ($step == 2) {
            return [
                'phone_number' => 'required',
                'email' => 'required|email'
            ];
        } elseif ($step == 3) {
            return [
                'avatar' => 'required'
            ];
        } else {
            return [
                'categories' => 'required'
            ];
        }
    }

    public function getByCategories()
    {
        return response()->json(['id' => request()->category_id]);
    }
}
