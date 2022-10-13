<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UserUpdateRequest;
use App\Http\Requests\PhoneNumberRequest;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Session;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserAPIController extends Controller
{

    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json($users);
    }


    function login(UserLoginRequest $request): JsonResponse
    {
        $request->authenticate();
        /** @var User $user */
        $user = auth()->user();
        $accessToken = $user->createToken('authToken')->accessToken;

        $expiresAt = now()->addMinutes(2); /* keep online for 2 min */
        Cache::put('user-is-online-' . $user->id, true, $expiresAt);

        /* last seen */
        User::query()->where('id', $user->id)->update(['last_seen' => now()]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => asset('storage/' . $user->avatar),
                'balance' => WalletBalance::query()->where(['user_id' => $user->id])->first()?->balance,
                'phone_number' => correctPhoneNumber($user->phone_number),
                'email_verified' => boolval($user->is_email_verified),
                'phone_verified' => boolval($user->is_phone_number_verified),
                'role_id' => $user->role_id,
            ],
            'access_token' => $accessToken,
            'socialpas' => $user->has_password
        ]);
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
    public function reset_submit(PhoneNumberRequest $request): JsonResponse
    {

        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $data['phone_number'])->firstOrFail();
        if(!($user->verify_code)){
            $message = rand(100000, 999999);
        }else{
            $message = $user->verify_code;
        }
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $message = "USer.Uz ". __("Код подтверждения") . ' ' . $message;
        $phone_number = $user->phone_number;
        SmsMobileService::sms_packages(correctPhoneNumber($phone_number),$message);
        session(['phone' => $data['phone_number']]);

        return response()->json(['success' => true, 'message' => __('СМС-код отправлен!')]);
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
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $data['phone_number'])->firstOrFail();
        $user->password = Hash::make($data['password']);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => __('Пароль был изменен')
        ]);

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
            'phone_number' => 'required|numeric'
        ]);
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $data['phone_number'])->firstOrFail();

        if ($data['code'] === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return response()->json(['success' => true, 'message' => __('Введите новый пароль')]);
            } else {
                abort(419);
            }
        } else {
            return response()->json(['success' => false, 'message' => __('Код ошибки')]);
        }
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            unset($data['password_confirmation']);
            /** @var User $user */
            $user = User::query()->create($data);
            $user->update(['phone_number' => $data['phone_number'] . '_' . $user->id]);
            $wallBal = new WalletBalance();
            $wallBal->balance = setting('admin.bonus');
            $wallBal->user_id = $user->id;
            $wallBal->save();
            $user->api_token = Str::random(60);
            $user->remember_token = Str::random(60);

            $user->save();
            Auth::login($user);
            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            return response()->json(['user' => auth()->user(), 'access_token' => $accessToken, 'socialpas' => $user->has_password]);
        } catch (ValidationException $e) {
            return response()->json(array_values($e->errors()));
        }
    }


    public function update(UserUpdateRequest $request, $id)
    {
        $data = $request->validated();
        $user = User::query()->where('id', $id)->firstOrFail();
        $user->update($data);

        return response()->json(['success' => true, 'message' => __('Данные пользователя обновлены!')]);
    }

    public function getSupportId()
    {
        /** @var User $user */
        $user = User::query()->findOrFail(setting('site.moderator_id'));
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => url('/storage') . '/' . $user->avatar,
                'last_seen' => $user->last_seen
            ]
        ]);
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
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        if ($request->get('device_id')) {
            Session::query()
                ->where('user_id', $user->id)
                ->where('device_id', $request->get('device_id'))
                ->delete();
        }
        return response()->json([
            'success' => true,
            'message' => __('Успешно вышел из системы')
        ]);
    }
}
