<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialRequest;
use App\Models\User;
use App\Services\User\SocialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAPIController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/social-login",
     *     tags={"Socials"},
     *     summary="Socials Google, Facebook and Apple",
     *     description="[**Telegram :** https://t.me/c/1334612640/233](https://t.me/c/1334612640/233).",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="type",
     *                    description="variantlardan biri tanlanadi",
     *                    type="string",
     *                    enum={"google", "facebook","apple"}
     *                 ),
     *                 @OA\Property (
     *                    property="access_token",
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
     * )
     */
    public function login(SocialRequest $request): JsonResponse
    {
        $data = $request->validated();
        $provider = match ($data['type']) {
            'google' => 'google',
            'facebook' => 'facebook',
            'apple' => 'apple',
            default => [],
        };
        return SocialService::login($provider, $data['access_token']);
    }

    // login with google
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleProviderCallback(Request $request, $provider)
    {
        $user = Socialite::driver($provider)->user();

        $auth_user = $this->findOrCreateUser($user, $provider);

        Auth::login($auth_user, true);
    }

    public function findOrCreateUser($user, $provider)
    {
        $authUser = User::query()->where('email', $user->email)->first();

        if ($authUser) {
            return $authUser;
        }

        $name = explode(' ', $user->name);

        return User::query()->create([
            'first_name' => $name[0],
            'last_name' => $name[1] ?? '',
            'email' => $user->email,
            'provider' => $provider,
            'provider_id' => $user->id,
            'avatar' => $user->avatar
        ]);
    }
}
