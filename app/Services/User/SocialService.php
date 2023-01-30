<?php

namespace App\Services\User;

use App\Http\Resources\PerformerIndexResource;
use App\Models\User;
use App\Models\WalletBalance;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;

class SocialService
{
    public static function login($provider, $token) {

        try {
            $providerUser = Socialite::driver($provider)->userFromToken($token);

            $query = User::query()
                ->where($provider . '_id', '=', $providerUser->id);

            if ($providerUser->email !== null) {
                $query->orWhere('email', '=', $providerUser->email);
            }

            $user = $query->withTrashed()->first();

            // if there is no record with these data, create a new user
            if ($user === null) {
                $user = User::query()->create([
                    $provider . '_id' => $providerUser->id,
                    'name' => $providerUser->name,
                    'email' => $providerUser->email,
                    'is_email_verified' => 1,
                    'avatar' => $provider !== 'apple' ? self::get_avatar($providerUser) : null
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

            $user->update([$provider . '_id' => $providerUser->id]);

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

    public static function get_avatar($user)
    {
        $fileContents = file_get_contents($user->getAvatar());
        File::put(public_path() . '/storage/user-avatar/' . $user->getId() . ".jpg", $fileContents);
        return 'user-avatar/' . $user->getId() . ".jpg";
    }
}
