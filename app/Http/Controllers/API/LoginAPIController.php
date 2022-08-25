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
            ->where($column, $data['type'] == 'phone_number' ? "+" . $data['data'] : $data['data'])
            ->where($verified, 1)->exists()
        ) {
            /** @var User $user */
            $user = auth()->user();
            $user->$column = $data['type'] == 'phone_number' ? "+" . $data['data'] : $data['data'];
            $user->$verified = 0;
            $user->save();
            if ($data['type'] == 'phone_number') {
                VerificationService::send_verification($data['type'], $user, phone_number: $data['data']);
            } else {
                VerificationService::send_verification($data['type'], $user, email: $data['data']);
            }

            return response()->json([
                'success' => true,
                'message' => $data['type'] == 'email' ? __('Ваша ссылка для подтверждения успешно отправлена!') : __('Код отправлен!')
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => $data['type'] == 'email' ? __('Пользователь с такой почтой уже существует!') : __('Пользователь с таким номером уже существует!')
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/account/verification/email",
     *     tags={"Verification"},
     *     summary="Email verification",
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
    public function send_email_verification()
    {
        VerificationService::send_verification('email', auth()->user());
        return response()->json(['success' => true, 'message' => __('Ваша ссылка для подтверждения успешно отправлена!')]);
    }

    /**
     * @OA\Get(
     *     path="/api/account/verification/phone",
     *     tags={"Verification"},
     *     summary="Phone verification",
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
    public function send_phone_verification()
    {
        /** @var User $user */
        $user = auth()->user();
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return response()->json(['success' => true, 'message' => __('Код отправлен!')]);
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
            if ($request->get('code') == $user->verify_code || $request->get('code') == setting('admin.CONFIRM_CODE')) {
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


    /**
     * @OA\Post(
     *     path="/api/account/change/email",
     *     tags={"Verification"},
     *     summary="Change email",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
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
    public function change_email(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($request->get('email') == $user->email) {
            return response()->json([
                'success' => false,
                'message' => __('Error, Your email is given'),
                'data' => $request->get('email'),
            ]);
        } else {
            $request->validate([
                'email' => 'required|unique:users|email'
            ], [
                'email.required' => __('login.email.required'),
                'email.email' => __('login.email.email'),
                'email.unique' => __('login.email.unique'),
            ]);
            $user->email = $request->get('email');
            $user->save();
            VerificationService::send_verification('email', $user);
            return response()->json(['message' => 'Verification link is send to your email', 'success' => true]);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/account/change/phone",
     *     tags={"Verification"},
     *     summary="Change phone number",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="phone_number",
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
    public function change_phone_number(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        if ($request->get('phone_number') == $user->phone_number) {
            return response()->json([
                'email-message' => 'Error, Your phone',
                'email' => $request->get('email')
            ]);
        } else {
            $request->validate([
                'phone_number' => 'required|unique:users|min:9'
            ],
                [
                    'phone_number.required' => __('login.phone_number.required'),
                    'phone_number.regex' => __('login.phone_number.regex'),
                    'phone_number.unique' => __('Этот номер зарегистрирован'),
                    'phone_number.min' => __('login.phone_number.min'),
                ]
            );
            $user->phone_number = $request->get('phone_number');
            $user->save();
            VerificationService::send_verification('phone_number', $user, $user->phone_number);

            return response()->json([
                'message' => __('Код отправлен!'),
                'success' => true
            ]);
        }
    }


}
