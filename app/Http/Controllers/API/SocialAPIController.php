<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SocialRequest;
use App\Http\Resources\PerformerIndexResource;
use App\Models\User;
use App\Models\WalletBalance;
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
     *     path="/api/social-login",
     *     tags={"Socials"},
     *     summary="Socials Google and Facebook",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="type",
     *                    description="0 - bu Google",
     *                    type="integer",
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
    public function login(SocialRequest $request)
    {
        try {
            $data = $request->validated();
            if ($data['type'] === 0) {
                $provider = 'google';
            } else {
                $provider = 'facebook';
            }
            $providerUser = Socialite::driver($provider)->userFromToken($data['access_token']);
            $user = User::query()
                ->where($provider . '_id', $providerUser->id)
                ->orWhere('email', $providerUser->email)
                ->first();
            // if there is no record with these data, create a new user
            if ($user === null) {
                $user = User::create([
                    $provider . '_id' => $providerUser->id,
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'is_email_verified' => 1,
                ]);
                $wallBal = new WalletBalance();
                $wallBal->balance = setting('admin.bonus');
                $wallBal->user_id = $user->id;
                $wallBal->save();
            }
            if (!$user->isActive()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Аккаунт отключен'),
                ]);
            }
            // create a token for the user, so they can login
            Auth::login($user);
            $accessToken = $user->createToken('authToken')->accessToken;
            // return the token for usage
            return response()->json([
                'user' => new PerformerIndexResource(auth()->user()),
                'access_token' => $accessToken,
                'socialpas' => $user->has_password
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
