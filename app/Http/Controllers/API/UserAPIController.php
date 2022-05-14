<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\PhoneNumberRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Models\WalletBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PlayMobile\SMS\SmsService;

class UserAPIController extends Controller
{

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }


    function login(UserLoginRequest $request)
    {
        $request->authenticate();
        $user = auth()->user();
        $accessToken = $user->createToken('authToken')->accessToken;

        $balance = WalletBalance::query()->where(['user_id' => $user->id])->firstOrFail();
        if (isset($balance))
            $userBalance = $balance->balance;
        else
            $userBalance = null;
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => asset('storage/'.$user->avatar),
                'balance' => $userBalance,
                'phone_number' => $user->phone_number,
                'email_verified' => boolval($user->is_email_verified),
                'phone_verified' => boolval($user->is_phone_number_verified),
                'role_id' => $user->role_id,
            ],
            'access_token'=>$accessToken]);

    }


    /**
     * @OA\Post(
     *     path="/api/reset",
     *     tags={"Reset Password"},
     *     summary="Reset password by phone - sending code",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 @OA\Property (
     *                     property="phone_number",
     *                     type="integer",
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
     * )
     */
    public function reset_submit(PhoneNumberRequest $request)
    {

        $data = $request->validated();
        $user = User::query()->where('phone_number', $data['phone_number'])->firstOrFail();
        $sms_otp = rand(100000, 999999);
        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        (new SmsService())->send(preg_replace('/[^0-9]/', '', $user->phone_number), $sms_otp);
        session(['phone' => $data['phone_number']]);

        return response()->json(['success' => true, 'message' => "SMS Code is send!"]);
    }


    /**
     * @OA\Post(
     *     path="/api/reset/password",
     *     tags={"Reset Password"},
     *     summary="Enter a New password",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 @OA\Property (
     *                     property="phone_number",
     *                     type="number",
     *                 ),
     *                 @OA\Property (
     *                     property="password",
     *                     type="string",
     *                     format="password",
     *                 ),
     *                 @OA\Property (
     *                     property="password_confirmation",
     *                     type="string",
     *                     format="password",
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
     * )
     */
    public function reset_password_save(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::query()->where('phone_number',$data['phone_number'])->firstOrFail();
        $user->password = Hash::make($data['password']);
        $user->save();

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'access_token'=>$accessToken]);

    }


    /**
     * @OA\Post(
     *     path="/api/code",
     *     tags={"Reset Password"},
     *     summary="Submit SMS code",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema (
     *                 @OA\Property (
     *                     property="code",
     *                     type="number",
     *                 ),
     *                 @OA\Property (
     *                     property="phone_number",
     *                     type="number",
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
     * )
     */
    public function reset_code(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|numeric|min:6',
            'phone_number' => 'required|numeric|exists:users'
        ]);

        $user = User::query()->where('phone_number', $data['phone_number'])->firstOrFail();

        if ($data['code'] == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return response()->json(['success'=> true, 'message' => 'Enter a new password']);
            } else {
                abort(419);
            }
        }else{
            return response()->json(['success'=> false, 'message' => 'Error Code']);
        }
    }

    public function register(UserRegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            unset($data['password_confirmation']);
            $user = User::create($data);
            $user->api_token = Str::random(60);
            $user->remember_token = Str::random(60);

            $user->save();
            Auth::login($user);
            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            return response()->json(['user' => auth()->user(), 'access_token'=>$accessToken]);
        } catch (ValidationException $e) {
            return response()->json(array_values($e->errors()));
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|min:5|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|max:16',
            ]);

            $user = User::query()->where('id', $id)->firstOrFail();
            $user->update($request->all());

            return response()->json(['status' => true, 'message' => 'User data updated!']);
        } catch (ValidationException $e) {
            return response()->json(array_values($e->errors()));
        }
    }

    /**
     * @OA\DELETE(
     *     path="/api/delete",
     *     tags={"Profile"},
     *     summary="Delete User",
     *     @OA\Response(
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
    public function destroy()
    {
        auth()->user()->delete();
        return response()->json(['status' => true, 'message' => 'User data deleted!']);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"UserAPI"},
     *     summary="User logout",
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
    function logout()
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
    }
}
