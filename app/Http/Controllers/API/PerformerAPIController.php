<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\BecomePerformerEmailPhone;
use App\Http\Requests\BecomePerformerRequest;
use App\Http\Requests\PerformerRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\PerformerIndexResource;
use App\Http\Resources\PerformerPaginateResource;
use App\Http\Resources\ReviewPaginationResource;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Symfony\Component\String\s;

class PerformerAPIController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/performers",
     *     tags={"PerformersAPI"},
     *     summary="Get All Performers",
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
    public function service(Request $request)
    {
        $performers = User::where('role_id', 2);
        if (isset($request->online))
        {
            $date = Carbon::now()->subMinutes(2)->toDateTimeString();
            $performers = $performers->where('role_id', 2)->where('last_seen', ">=",$date);
        }

        return PerformerIndexResource::collection($performers->paginate($request->per_page));
    }

    public function online_performers()
    {
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        $performers = User::where('role_id', 2)->where('last_seen', ">=",$date)->paginate();
        return new PerformerPaginateResource($performers);
    }

    /**
     * @OA\Get(
     *     path="/api/performers/{performer}",
     *     tags={"PerformersAPI"},
     *     summary="Get Performer By ID",
     *     @OA\Parameter(
     *          in="path",
     *          name="performer",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          ),
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

    public function becomePerformerData(BecomePerformerRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $user->update($data);

        return response()->json(['success' => 'true', 'message' => 'Successfully updated']);
    }
    public function becomePerformerEmailPhone(BecomePerformerEmailPhone $request)
    {
        $request->validated();
        return response()->json(['success' => 'true', 'message' => 'Successfully updated']);
    }

    public function becomePerformerAvatar(Request $request)
    {
        $data = $request->validate(['avatar'=>'required']);
        $avatar = $data['avatar'];

        $data['role_id'] = 2;
        $name = Storage::put('public/uploads', $avatar);
        $name = str_replace('public/', '', $name);
        $data['avatar'] = $name;
        auth()->user()->update($data);

        return response()->json(['success' => true, 'message' => 'true']);


    }


    public function becomePerformerCategory(Request $request)
    {
        $data = $request->validate(['category_id' => 'required|string']);

        auth()->user()->update($data);

        return response()->json(['success' => true, "message" => 'successfully updated']);

    }

    public function reviews(Request $request)
    {
        $from_performer = $request->from_performer;
        $reviews = Review::query()->whereHas('task')->whereHas('user')->where('user_id',auth()->user()->id)->paginate();

        return new ReviewPaginationResource($reviews);
    }

    public function getByCategories()
    {
        return response()->json(['id' => request()->category_id]);
    }
}
