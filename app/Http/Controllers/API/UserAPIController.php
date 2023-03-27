<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\{Api\ResetCodeRequest,
    Api\ResetPasswordRequest, PhoneNumberRequest,
    Api\UserLoginRequest, Api\UserRegisterRequest};
use App\Models\User;
use Exception;
use App\Services\{Response, User\UserService};
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Validation\ValidationException;

class UserAPIController extends Controller
{

    use Response;
    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }


    public function login(UserLoginRequest $request): JsonResponse
    {
        $request->authenticate();
        /** @var User $user */
        $user = auth()->user();
        return $this->service->login_api_service($user);
    }


    /**
     * @OA\Post(
     *     path="/api/reset",
     *     tags={"Reset Password"},
     *     summary="Reset password by phone - sending code",
     *     @OA\Parameter (
     *          in="query",
     *          description="Telefon raqam kiritiladi(masalan,'998949999999')",
     *          name="phone_number",
     *          @OA\Schema (
     *              type="string"
     *          )
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
     * @throws Exception
     */
    public function reset_submit(PhoneNumberRequest $request): JsonResponse
    {
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $this->service->reset_submit_api($phone_number);
        return response()->json([
            'success' => true,
            'message' => __('СМС-код отправлен!'),
            'data' => $data
        ]);
    }


    /**
     * @OA\Post(
     *     path="/api/reset/password",
     *     tags={"Reset Password"},
     *     summary="Enter a New password",
     *     @OA\Parameter (
     *          in="query",
     *          description="Telefon raqam kiritiladi",
     *          name="phone_number",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="Yangi parol kiritiladi",
     *          name="password",
     *          @OA\Schema (
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter (
     *          in="query",
     *          description="Yangi parol takror kiritiladi",
     *          name="password_confirmation",
     *          @OA\Schema (
     *              type="string"
     *          )
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
    public function reset_password_save(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $password = $data['password'];
        return $this->service->reset_save($phone_number, $password);
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
     *                     description="telefon raqamga yuborilgan kod kiritiladi",
     *                     type="number",
     *                 ),
     *                 @OA\Property (
     *                     property="phone_number",
     *                     description="telefon raqam kiritiladi",
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
    public function reset_code(ResetCodeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $phone_number = $data['phone_number'];
        $code = $data['code'];
        return $this->service->reset_code($phone_number, $code);
    }

    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            return $this->service->register_api_service($data);
        } catch (ValidationException $e) {
            return response()->json(array_values($e->errors()));
        }
    }

    /**
     * @OA\Get(
     *     path="/api/support-admin",
     *     tags={"Chat"},
     *     summary="Support admin",
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
    public function getSupportId(): JsonResponse
    {
       return $this->service->getSupportId();
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
    public function logout(Request $request): JsonResponse
    {
        $device_id = $request->get('device_id');
        return $this->service->logout($device_id);
    }
}
