<?php

namespace App\Http\Controllers;

use App\Mail\MessageEmail;
use App\Models\User;
use App\Models\Task;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use PlayMobile\SMS\SmsService;
use Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function code()
    {
        return view('auth.register_code');
    }

    public function signup()
    {
        return view('auth.signup');
    }

    public function reset()
    {
        return view('auth.reset');
    }

    public function confirm()
    {
        return view('auth.confirm');
    }

    public function code_submit(Request $request)
    {

        $user = auth()->user();
        if ($request->code == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect('/profile');
            } else {
            }
        }
    }


    public function reset_submit(Request $request)
    {
        if (!str_starts_with($request['phone_number'], '+998')) {
            $request['phone_number'] = '+998' . $request['phone_number'];
        }
        $data = $request->validate([
            'phone_number' => 'required|integer|exists:users'
        ]);
        $user = User::query()->where('phone_number', $data['phone_number'])->first();
        if (!$user) {
            return back()->with([
                'message' => "This phone number does not have an account!"
            ]);
        }
        $sms_otp = rand(100000, 999999);
        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        (new SmsService())->send(preg_replace('/[^0-9]/', '', $user->phone_number), $sms_otp);
        session()->put('verifications', ['key' => 'phone_number', 'value' => $data['phone_number']]);

        return redirect()->route('user.reset_code_view');
    }

    public function reset_by_email(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        $user = User::query()->where('email', $data['email'])->first();
        if (!$user) {
            return back()->with([
                'message' => "This Email does not have an account!"
            ]);
        }
        $sms_otp = rand(100000, 999999);
        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        session()->put('verifications', ['key' => 'email', 'value' => $data['email']]);

        Mail::to($user->email)->send(new MessageEmail($sms_otp));
        Alert::success('Congrats', 'Your verification code has been successfully sent to  ' . $user->email);

        return redirect()->route('user.reset_code_view_email');
    }

    public function reset_code(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|numeric|min:6'
        ]);
        $verifications = $request->session()->get('verifications');

        $user = User::query()->where($verifications['key'], $verifications['value'])->first();

        if ($data['code'] == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect()->route('user.reset_password');
            } else {
                abort(419);
            }
        } else {
            return back()->with(['error' => 'Error Code']);
        }
    }


    public function reset_code_view()
    {

        return view('auth.code');
    }

    public function reset_password(Request $request)
    {
        return view('auth.confirm_password');
    }

    public function reset_password_save(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);
        $user = User::query()->where($request->session()->get('verifications')['key'], $request->session()->get('verifications')['value'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();
        auth()->login($user);
        return redirect('/profile');
    }

    public function verifyProfil(Request $request, User $user, Task $task)
    {

        $task = Task::query()->find($request->get('for_ver_func'));
        $request->validate(
            ['sms_otp' => 'required'],
            ['sms_otp.required' => 'Требуется заполнение!']
        );

        if ($request->sms_otp == $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->update(['is_phone_number_verified' => 1]);
                Task::findOrFail($request->for_ver_func)->update(['status' => 1, 'user_id' => $user->id, 'phone' => $user->phone_number]);
                auth()->login($user);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $request->for_ver_func);
            } else {
                auth()->logout();
                return back()->with('expired_message', __('lang.contact_expired'));
            }
        } else {
            auth()->logout();
            return back()->with('incorrect_message', __('lang.contact_notVerify'));
        }

    }

    public function verifyProfil2(Request $request, User $user, Task $task, $data)
    {

        $task = Task::query()->find($request->get('for_ver_func'));
        $request->validate(
            ['sms_otp' => 'required'],
            ['sms_otp.required' => 'Требуется заполнение!']
        );

        if ($request->sms_otp == $task->verify_code) {
            if (strtotime($task->verify_expiration) >= strtotime(Carbon::now())) {
                /*$task->update(['is_phone_number_verified' => 1]);*/
                Task::findOrFail($request->for_ver_func)->update(['status' => 1, 'user_id' => $user->id, 'phone' => $data]);
                auth()->login($user);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $request->for_ver_func);
            } else {
                auth()->logout();
                return back()->with('expired_message', __('lang.contact_expired'));
            }
        } else {
            auth()->logout();
            return back()->with('incorrect_message', __('lang.contact_notVerify'));
        }

    }

}
