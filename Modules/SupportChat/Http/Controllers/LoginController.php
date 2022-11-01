<?php

namespace Modules\SupportChat\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SupportChat\Http\Requests\LoginRequest;
use Modules\SupportChat\Http\Requests\AdminRequest;
use Modules\SupportChat\Models\Questions;
use App\Models\User;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use SergiX44\Nutgram\Nutgram;

class LoginController extends Controller
{

    public function question(){
        $questions = Questions::latest()->get();
        return view('supportchat::question',compact('questions'));
    }

    public function login(){
        $user_id = Cookie::get('user_uz_support_chat_id');
        if ($user_id != null) {
            $user = User::query()->find($user_id);
            if (isset($user)) {
                auth()->login($user);
                return redirect('chatify/'.setting("site.admin_id"));
            }
        }
        return view('supportchat::signin');
    }

    public function login_store(LoginRequest $request){
        $data = $request->validated();
        $user = User::create($data);
        $phone_number = $data['phone_number'];
        VerificationService::send_verification(null,$user, $phone_number);
        auth()->login($user);
        return view('supportchat::verify',['user' => $user]);
    }

    public function verify_store(Request $request,User $user){
        $request->validate(
            ['code' => 'required'],
            ['code.required' => __('Требуется заполнение!')]
        );
        if ((int)$request->get('code') === (int)$user->verify_code) {
            $bot = new Nutgram(setting('site.TELEGRAM_TOKEN'));
            $bot->sendMessage($user->name . ' ' . $user->phone_number, ['chat_id'=>setting('site.CHANNEL_ID')]);
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->phone_verified_at = 1;
                $user->save();

                Cookie::queue('user_uz_support_chat_id', $user->id, 1 * 365 * 24 * 60);
                return redirect('chatify/'.setting("site.admin_id"));
            } else {
                return view('supportchat::signin')->with('expired_message', __('Срок действия отправленного кода истек'));
            }
        }else{
            return view('supportchat::verify',['user' => $user])->with('incorrect_message', __('Код неверный'));
        }
    }

    public function lang($lang)
    {
        Session::put('lang', $lang);
        return redirect()->back();
    }

    public function admin_login(){
        return view('supportchat::admin_login');
    }

    public function admin_login_store(AdminRequest $request){
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()
            ->where('name', $data['name'])
            ->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->with('password_incorrect', __('Неверный пароль'));
        }
        auth()->login($user);
        return redirect('/chatify')->with('password_incorrect', __('Неверный пароль'));
    }

}
