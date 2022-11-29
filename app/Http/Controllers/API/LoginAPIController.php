<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerifyCredentialsRequest;
use App\Models\User;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoginAPIController extends Controller
{
    public function verifyCredentials(VerifyCredentialsRequest $request): JsonResponse
    {
        $data = $request->validated();
        $column = $data['type'];
        $verified = 'is_' . $column . '_verified';
        if (!User::query()
            ->where($column, $data['type'] === 'phone_number' ? correctPhoneNumber($data['data']) : $data['data'])
            ->whereNot('id', auth()->id())
            ->exists()
        ) {
            /** @var User $user */
            $user = auth()->user();
            Cache::put($user->id . 'user_' . $column , $data['type'] === 'phone_number' ? correctPhoneNumber($data['data']) : $data['data']);

            if ($data['type'] === 'phone_number') {
                VerificationService::send_verification($data['type'], $user, phone_number: $data['data']);
            } else {
                $user = Auth::user();
                $user->email = $data['data'];
                $user->save();
                VerificationService::send_verification($data['type'], $user, email: $data['data']);
            }

            return response()->json([
                'success' => true,
                'message' => $data['type'] === 'email' ? __('Ваша ссылка для подтверждения успешно отправлена!') : __('Код отправлен!')
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $data['type'] === 'email' ? __('Пользователь с такой почтой уже существует!') : __('Пользователь с таким номером уже существует!')
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/account/verification/phone",
     *     tags={"Verification"},
     *     summary="Verification phone",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="code",
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
    public function verify_phone(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);
        /** @var User $user */
        $user = auth()->user();

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($request->get('code') === $user->verify_code || $request->get('code') === setting('admin.CONFIRM_CODE')) {
                $user->phone_number = correctPhoneNumber(Cache::get($user->id . 'user_phone_number'));
                $user->is_phone_number_verified = 1;
                $user->save();
                Cache::forget($user->id . 'user_phone_number');
                return response()->json([
                    'success' => true,
                    'message' => __('Ваш телефон успешно подтвержден')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Неправильный код!')
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => __('Срок действия номера истек')
            ]);
        }
    }
}
