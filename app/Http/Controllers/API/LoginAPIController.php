<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerifyCredentialsRequest;
use App\Models\User;
use App\Services\Task\LoginAPIService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginAPIController extends Controller
{
    public LoginAPIService $service;

    public function __construct(LoginAPIService $loginAPIService)
    {
        $this->service = $loginAPIService;
    }

    /**
     * @OA\Get(
     *     path="/api/account/verify",
     *     tags={"Verification"},
     *     summary="Profilni tasdiqlash uchun api",
     *     description="[**Telegram :** https://t.me/c/1334612640/205](https://t.me/c/1334612640/205).",
     *     @OA\Parameter(
     *          in="query",
     *          name="type",
     *          required=true,
     *          description="email yoki phone_number",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="data",
     *          required=true,
     *          description="email yoki phone_number qiymati",
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
     *     ),
     *     security={
     *         {"token": {}}
     *     },
     * )
     * @throws Exception
     */
    public function verifyCredentials(VerifyCredentialsRequest $request): JsonResponse
    {
        $data = $request->validated();
        return $this->service->verifyCredentials($data);
    }


    /**
     * @OA\Post(
     *     path="/api/account/verification/phone",
     *     tags={"Verification"},
     *     summary="Telefon raqamni tasdiqlash",
     *     description="[**Telegram :** https://t.me/c/1334612640/170](https://t.me/c/1334612640/170).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="code",
     *                    description="Telefon raqamga yuborilgan kod kiritiladi",
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
        $code = $request->get('code');
        /** @var User $user */
        $user = auth()->user();

        return $this->service->verify_phone($user, $code);
    }

    /**
     * @OA\Post(
     *     path="/api/account/verification/email",
     *     tags={"Verification"},
     *     summary="Emailni tasdiqlash",
     *     description="[**Telegram :** https://t.me/c/1334612640/239](https://t.me/c/1334612640/239).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="code",
     *                    description="Emailga yuborilgan kod kiritiladi",
     *                    type="integer",
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
    public function verify_email(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required'
        ]);
        $code = $request->get('code');
        /** @var User $user */
        $user = auth()->user();
        return $this->service->verify_email($user, $code);

    }
}
