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
     *                 @OA\Property (
     *                    property="email",
     *                    type="email",
     *                 ),
     *                 @OA\Property (
     *                    property="avatar",
     *                    type="string",
     *                    
     *                 ),
     *                 @OA\Property (
     *                    property="server_code",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="name",
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
    public function loginWithFacebook(Request $request)
    {
        $data = $request->validate([
           'google_id' => 'nullable',
           'facebook_id' => 'required|nullable',
           'email' => 'nullable', //email
           'name' => 'string', //required
           'avatar' => 'string', //required
           'server_code' => 'string', //required
        ],
            [
                'id.required' => "id  required",
                'email.required' => "Email  required",
                'name.required' => "name  required",
                'avatar.required' => "avatar  required",
                'server_code.required' => "server_code  required",

            ]
        );
        $data['email'] = isset($data['email'])? $data['email']:null;
        if (isset($data['google_id']))
            $findUser = User::orWhere('email', $data['email'])->orWhere('google_id', $data['google_id'])->first();
        else if (isset($data['facebook_id'])    )
            $findUser = User::orWhere('email', $data['email'])->orWhere('facebook_id', $data['facebook_id'])->first();


        if (isset($data['google_id']) || isset($data['facebook_id']))
        {
            if ($findUser) {
                if (isset($data['google_id']))
                    $findUser->google_id = $data['google_id'];
                else if (isset($data['facebook_id']))
                    $findUser->facebook_id = $data['facebook_id'];
                $findUser->save();
                Auth::login($findUser);
                $accessToken = auth()->user()->createToken('authToken')->accessToken;
                return response(['success' => true,'user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);

            } else {
                $new_user = new User();
                $new_user->name = $data['name'];
                $new_user->email = $data['email'];
                if (isset($data['google_id']))
                    $new_user->google_id = $data['google_id'];
                else if (isset($data['facebook_id']))
                    $new_user->facebook_id = $data['facebook_id'];
                $new_user->avatar = self::get_avatar($data['avatar'],$data['id']);
                $new_user->password = encrypt('123456');
                $new_user->save();
                Auth::login($new_user);

                $accessToken = auth()->user()->createToken('authToken')->accessToken;

                return response()->json(['success' => true, 'user' => new PerformerIndexResource(auth()->user()), 'access_token'=>$accessToken]);
            }
        }
        return response()->json(['success'  => false, 'message' => 'Not required data']);



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
     *                 @OA\Property (
     *                    property="email",
     *                    type="email",
     *                 ),
     *                 @OA\Property (
     *                    property="avatar",
     *                    type="string",
     *                    
     *                 ),
     *                 @OA\Property (
     *                    property="server_code",
     *                    type="string",
     *                 ),
     *                 @OA\Property (
     *                    property="name",
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
    public function loginWithGoogle(Request $request)
    {

        try {
            $data = $request->validate([
                'id' => 'required',
                'email' => 'nullable', //email
                'name' => 'string', //required
                'avatar' => 'string', //required
                'server_code' => 'string', //required
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
