<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerifyCredentialsRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PlayMobile\SMS\SmsService;
use RealRashid\SweetAlert\Facades\Alert;

class LoginAPIController extends Controller
{

    public function verifyCredentials(VerifyCredentialsRequest $request)
    {
        $data = $request->validated();
        $column = $data['type'];
        $code = self::sendVerification($data['type'], $data['data']);
        /** @var User $user */
        $user = auth()->user();
        $user->$column = $data['data'];
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Success'
        ]);

    }

    public static function sendVerification($type, $value)
    {
        if ($type == 'phone_number') {

            $code = rand(100000, 999999);
            (new SmsService())->send($value, $code);
        } else {
            $code = sha1(time());
            $data = [
                'code' => $code,
                'user' => auth()->user()->id
            ];
            Mail::to($value)->send(new VerifyEmail($data));
        }
        return $code;
    }

    public static function send_verification($needle, $user)
    {
        if ($needle == 'email') {
            $code = sha1(time());
            $data = [
                'code' => $code,
                'user' => auth()->user()->id
            ];
            Mail::to($user->email)->send(new VerifyEmail($data));
        } else {
            $code = rand(100000, 999999);
            (new SmsService())->send($user->phone_number, $code);
        }
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
    }

    /**
     * @OA\Get(
     *     path="/api/account/verification/email",
     *     tags={"Verification"},
     *     summary="Email verification",
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
    public function send_email_verification()
    {
        self::send_verification('email', auth()->user());
        return response()->json(['success' => true, 'message' => 'success']);
    }

    /**
     * @OA\Get(
     *     path="/api/account/verification/phone",
     *     tags={"Verification"},
     *     summary="Phone verification",
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
    public function send_phone_verification()
    {
        self::send_verification('phone', auth()->user());
        return response()->json(['message' => 'success']);
    }


    public static function verifyColum($request, $needle, $user, $hash)
    {
        $needle = 'is_' . $needle . "_verified";

        $result = false;

        if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
            if ($hash == $user->verify_code || $hash == setting('admin.CONFIRM_CODE')) {
                $user->$needle = 1;
                $user->save();
                $result = true;
                if ($needle != 'is_phone_number_verified')
                    self::send_verification('phone', auth()->user());
            } else {
                $result = false;
            }
        } else {
            abort(419);
        }
        return $result;
    }


    public function verifyAccount(User $user, $hash, Request $request)
    {
        self::verifyColum($request, 'email', $user, $hash);
        auth()->login($user);
        Alert::success(__('Congrats'), __('Your Email have successfully verified'));
        return redirect()->route('profile.profileData');

    }


    /**
     * @OA\Post(
     *     path="/api/account/verification/phone",
     *     tags={"Verification"},
     *     summary="Verification phone",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="code",
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
    public function verify_phone(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);
        if (self::verifyColum($request, __('phone_number'), auth()->user(), $request->code)) {
            return response()->json(['message' => __('success')]);
        } else {
            return response()->json([
                'message' => 'Code Error!',
                'success' => false
            ]);

        }
    }


    /**
     * @OA\Post(
     *     path="/api/account/change/email",
     *     tags={"Verification"},
     *     summary="Change email",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="email",
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
    public function change_email(Request $request)
    {

        $user = auth()->user();

        if ($request->email == $user->email) {
            return response()->json([
                'success' => false,
                'message' => __('Error, Your email is given'),
                'data' => $request->email,
            ]);
        } else {
            $request->validate([
                'email' => 'required|unique:users|email'
            ],
                [
                    'email.required' => __('login.email.required'),
                    'email.email' => __('login.email.email'),
                    'email.unique' => __('login.email.unique'),
                ]
            );
            $user->email = $request->email;
            $user->save();
            self::send_verification('email', $user);


            return response()->json(['message' => 'Verification link is send to your email', 'success' => true]);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/account/change/phone",
     *     tags={"Verification"},
     *     summary="Change phone number",
     *     @OA\RequestBody (
     *         required=true,
     *         @OA\MediaType (
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property (
     *                    property="phone_number",
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
    public function change_phone_number(Request $request)
    {

        $user = auth()->user();

        if ($request->get('phone_number') == $user->phone_number) {
            return response()->json([
                'email-message' => 'Error, Your phone',
                'email' => $request->email
            ]);
        } else {
            $request->validate([
                'phone_number' => 'required|unique:users|min:9'
            ],
                [
                    'phone_number.required' => __('login.phone_number.required'),
                    'phone_number.regex' => __('login.phone_number.regex'),
                    'phone_number.unique' => __('login.phone_number.unique'),
                    'phone_number.min' => __('login.phone_number.min'),
                ]
            );
            $user->phone_number = $request->phone_number;
            $user->save();
            self::send_verification('phone_number', auth()->user());

            return response()->json([
                'message' => __('Код отправлен!'),
                'success' => true
            ]);
        }
    }


}
