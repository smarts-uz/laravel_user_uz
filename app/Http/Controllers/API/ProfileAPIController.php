<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Http\Resources\UserIndexResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileAPIController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return new UserIndexResource($user);
    }

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'data' => $validator->errors(),
                'message' => "something went wrong"
            ]);
        }
        $user = auth()->user();
        if (Hash::check($request['old_password'], $user->password)) {
            $user->update(['password' => Hash::make($request['password'])]);

            return response()->json([
                'status' => true,
                'message' => "password successfully changed",
                'password' => 'password'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Old password wrong"
            ]);
        }


    }

    public function avatar(Request $request)
    {
        $image = $request->validate(['image' => 'required'])['image'];
        $name = md5(Carbon::now() . '_' . $image->getClientOriginalName() . '.' . $image->getClientOriginalExtension());
        $filepath = Storage::disk('public')->putFileAs('/images', $image, $name);
        $data['avatar'] = $filepath;
        auth()->user()->update($data);

        return response()->json(['success' => true]);

    }

    public function settings()
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'location' => $user->location,
            'age' => $user->age,
            'role_id' => $user->role_id,
            'description' => $user->description,
        ]);
    }


    /**
     *
     * @OA\Post (
     *     path="/api/settings/update",
     *     tags={"Profile"},
     *     summary="Update Settings",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="age",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="phone_number",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="location",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "email":"admin@admin.com",
     *                     "age":17,
     *                     "phone_number":"999098998",
     *                     "description":"Assalomu aleykum",
     *                     "location":"Xorazm viloyati",
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="email", type="string", example="admin@admin.com"),
     *              @OA\Property(property="age", type="integer", example=20),
     *              @OA\Property(property="phone_number", type="string", example="999098998"),
     *              @OA\Property(property="description", type="string", example="Assalomu aleykum"),
     *              @OA\Property(property="location", type="string", example="Xorazm viloyati"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     */
    public function updateData(UserUpdateDataRequest $request)
    {
        $data = $request->validated();
        if ($data['email'] != auth()->user()->email) {
            $data['is_email_verified'] = 0;
            $data['email_old'] = auth()->user()->email;
        }
        if ($data['phone_number'] != auth()->user()->phone_number) {
            $data['is_phone_number_verified'] = 0;
            $data['phone_number_old'] = auth()->user()->phone_number;
        }
        $user = Auth::user();
        $user->update($data);
        Alert::success(__('Настройки успешно сохранены'));
        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    public function cash()
    {
        return 1;
    }
}
