<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialRequest;
use App\Http\Resources\PerformerIndexResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;

class SocialAPIController extends Controller
{
    //login with facebook
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * @OA\Post(
     *     path="/api/login/callback",
     *     tags={"Social"},
     *     summary="Facebook",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="google_id",
     *                    type="integer",
     *                 ),
     *                 @OA\Property (
     *                    property="facebook_id",
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
     * )
     */
    public function loginWithFacebook(SocialRequest $request)
    {
        try {
            $token = $request->input('access_token');
            $providerUser = Socialite::driver('facebook')->userFromToken($token);
            $user = User::where('facebook_id', $providerUser->id)->first();
            // if there is no record with these data, create a new user
            if ($user == null) {
                $user = User::create([
                    'google_id' => $providerUser->id,
                ]);
            }
            // create a token for the user, so they can login
            Auth::login($user);
            $accessToken = $user->createToken('authToken')->accessToken;
            // return the token for usage
            return response()->json([
                'user' => new PerformerIndexResource(auth()->user()),
                'access_token' => $accessToken
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
                'message' => "Record not found"
            ]);
        }
    }


    // login with google
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }


    private static function get_avatar($avatar, $id)
    {
        $fileContents = file_get_contents($avatar);
        File::put(public_path() . '/storage/users-avatar/' . $id . ".jpg", $fileContents);
        $picture = 'users-avatar/' . $id . ".jpg";

        return $picture;
    }

    /**
     * @OA\Post(
     *     path="/api/login/google/callback",
     *     tags={"Social"},
     *     summary="Google",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="id",
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
     * )
     */
    public function loginWithGoogle(SocialRequest $request)
    {
        try {
            $token = $request->input('access_token');
            $providerUser = Socialite::driver('google')->userFromToken($token);
            $user = User::where('google_id', $providerUser->id)->first();
            // if there is no record with these data, create a new user
            if ($user == null) {
                $user = User::create([
                    'google_id' => $providerUser->id,
                ]);
            }
            // create a token for the user, so they can login
            Auth::login($user);
            $accessToken = $user->createToken('authToken')->accessToken;
            // return the token for usage
            return response()->json([
                'user' => new PerformerIndexResource(auth()->user()),
                'access_token' => $accessToken
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'data' => $e->getMessage(),
                'message' => "Record not found"
            ]);
        }
    }

    public function handleProviderCallback(Request $request, $provider)
    {
        $user = Socialite::driver($provider)->user();

        $auth_user = $this->findOrCreateUser($user, $provider);

        Auth::login($auth_user, true);
    }

    public function findOrCreateUser($user, $provider)
    {
        $authUser = User::where('email', $user->email)->first();

        if ($authUser) {
            return $authUser;
        }

        $name = explode(' ', $user->name);

        return User::create([
            'first_name' => $name[0],
            'last_name' => $name[1] ?? '',
            'email' => $user->email,
            'provider' => $provider,
            'provider_id' => $user->id,
            'avatar' => $user->avatar
        ]);
    }
}
