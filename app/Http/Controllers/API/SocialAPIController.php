<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerformerIndexResource;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Laravel\Socialite\Facades\Socialite;
use RealRashid\SweetAlert\Facades\Alert;

class SocialAPIController extends Controller
{
    //login with facebook
    public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function loginWithFacebook(Request $request)
    {
        $data = $request->validate([
           'id' => 'required',
           'email' => 'nullable|email',
           'name' => 'required|string',
           'avatar' => 'required|string',
           'server_code' => 'required|string',
        ],
            [
                'id.required' => "id  required",
                'email.required' => "Email  required",
                'name.required' => "name  required",
                'avatar.required' => "avatar  required",
                'server_code.required' => "server_code  required",

            ]
        );
        $findUser = User::orWhere('email', $data['email'])->orWhere('facebook_id', $data['id'])->first();


        if ($findUser) {
            $findUser->facebook_id = $data['id'];
            $findUser->save();
            Auth::login($findUser);
            $accessToken = auth()->user()->createToken('authToken')->accessToken;
            return response(['success' => true,'user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);

        } else {
            $new_user = new User();
            $new_user->name = $data['name'];
            $new_user->email = $data['email'];
            $new_user->facebook_id = $data['id'];
            $new_user->avatar = self::get_avatar($data['avatar'],$data['id']);
            $new_user->password = encrypt('123456');
            $new_user->save();
            Auth::login($new_user);

            $accessToken = auth()->user()->createToken('authToken')->accessToken;

            return response()->json(['user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);
        }
    }


    // login with google
    public function googleRedirect()
    {
        return Socialite::driver('google')->redirect();
    }


    private static function get_avatar($avatar,$id)
    {
        $fileContents = file_get_contents($avatar);
        File::put(public_path() . '/storage/users-avatar/' .$id  . ".jpg", $fileContents);
        $picture = 'users-avatar/' . $id . ".jpg";

        return $picture;
    }

    public function loginWithGoogle(Request $request)
    {

        try {
            $data = $request->validate([
                'id' => 'required',
                'email' => 'nullable|email',
                'name' => 'required|string',
                'avatar' => 'required|string',
                'server_code' => 'required|string',
            ],
                [
                    'id.required' => "id  required",
                    'email.required' => "Email  required",
                    'name.required' => "name  required",
                    'avatar.required' => "avatar  required",
                    'server_code.required' => "server_code  required",

                ]
            );
            $findUser = User::orWhere('email', $data['email'])->orWhere('google_id', $data['id'])->first();



            if ($findUser) {
                $findUser->google_id = $data['id'];
                $findUser->save();
                Auth::login($findUser);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;
                return response(['success' => true,'user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);

            } else {
                $new_user = new User();
                $new_user->name = $data['name'];
                $new_user->email = $data['email'];
                $new_user->google_id = $data['id'];
                $new_user->avatar = self::get_avatar($data['avatar'],$data['id']);
                $new_user->password = encrypt('123456');
                $new_user->save();
                Auth::login($new_user);

                $accessToken = auth()->user()->createToken('authToken')->accessToken;

                return response()->json(['user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);

            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
