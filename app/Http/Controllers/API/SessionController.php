<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SessionDeleteRequest;
use App\Http\Resources\SessionResource;
use App\Models\Session;
use App\Models\User;

class SessionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile/sessions",
     *     tags={"Profile"},
     *     summary="Get all sessions",
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
    public function index()
    {
        return $this->success(SessionResource::collection(Session::query()->where('user_id', auth()->id())->get()));
    }

    /**
     * @OA\Post(
     *     path="/api/profile/clear-sessions",
     *     tags={"Profile"},
     *     summary="Clear sessions without current session",
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
    public function clearSessions(SessionDeleteRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        Session::query()->where('user_id', $user->id)->whereNot('id', $request->get('session_id'))->delete();
        $user->tokens->each(function ($token, $key) use ($user) {
            if ((int)$token->id !== (int)$user->token()->id) {
                $token->delete();
            }
        });
        return $this->success('', __('Успешно удалено'));
    }
}
