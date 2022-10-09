<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerifyCredentialsRequest;
use App\Models\User;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginAPIController extends Controller
{
    public function verifyCredentials(VerifyCredentialsRequest $request)
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
            $user->$column = $data['type'] === 'phone_number' ? correctPhoneNumber($data['data']) : $data['data'];
            $user->$verified = 0;
            $user->save();
            if ($data['type'] === 'phone_number') {
                VerificationService::send_verification($data['type'], $user, phone_number: $data['data']);
            } else {
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
    public function verify_phone(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);
        /** @var User $user */
        $user = auth()->user();

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($request->get('code') === $user->verify_code || $request->get('code') === setting('admin.CONFIRM_CODE')) {
                $user->is_phone_number_verified = 1;
                $user->save();
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
