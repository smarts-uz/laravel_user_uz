<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SessionDeleteRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public UserService $service;

    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/profile/sessions",
     *     tags={"Profile"},
     *     summary="Get all sessions",
     *     description="[**Telegram :** https://t.me/c/1334612640/219](https://t.me/c/1334612640/219).",
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
    public function index(): JsonResponse
    {
        $user_id = auth()->id();
        $data = $this->service->sessionIndex($user_id);
        return $this->success($data);
    }

    /**
     * @OA\Post(
     *     path="/api/profile/clear-sessions",
     *     tags={"Profile"},
     *     summary="Clear sessions without current session",
     *     description="[**Telegram :** https://t.me/c/1334612640/220](https://t.me/c/1334612640/220).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="session_id",
     *                    description="Current session id",
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
    public function clearSessions(SessionDeleteRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $session_id = $request->get('session_id');
        return $this->service->clearSessions($user, $session_id);
    }
}
