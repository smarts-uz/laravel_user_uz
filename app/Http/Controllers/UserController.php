<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetRequest;
use App\Mail\MessageEmail;
use App\Models\User;
use App\Models\Task;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;


class UserController extends Controller
{

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

    public function reset_submit(ResetRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('phone_number', $data['phone_number'])->first();
        if (!$user) {
            return back()->with([
                'message' => __("Этот номер телефона не зарегистрирован!")
            ]);
        }
        $code = rand(100000, 999999);
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $phone_number = $user->phone_number;
        $message = "USer.Uz " . __("Код подтверждения") . ' ' . $code;
        SmsMobileService::sms_packages($phone_number, $message);

        session()->put('verifications', ['key' => 'phone_number', 'value' => $data['phone_number']]);

        return redirect()->route('user.reset_code_view');
    }

    public function reset_by_email(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email|exists:users'
        ]);
        /** @var User $user */
        $user = User::query()->where('email', $data['email'])->first();
        if (!$user) {
            return back()->with([
                'message' => __("Этот адрес электронной почты не имеет учетной записи!")
            ]);
        }
        $sms_otp = rand(100000, 999999);
        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        session()->put('verifications', ['key' => 'email', 'value' => $data['email']]);

        Mail::to($user->email)->send(new MessageEmail($sms_otp));
        Alert::success(__('Поздравляю'), __('Ваш проверочный код успешно отправлен на') . $user->email);

        return redirect()->route('user.reset_code_view_email');
    }

    public function reset_code(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|numeric|min:6'
        ]);
        $verifications = $request->session()->get('verifications');
        /** @var User $user */
        $user = User::query()->where($verifications['key'], $verifications['value'])->first();

        if ($data['code'] === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect()->route('user.reset_password');
            } else {
                abort(419);
            }
        } else {
            return back()->with(['error' => __('Код ошибки')]);
        }
    }

    public function reset_code_view()
    {
        return view('auth.code');
    }

    public function reset_password()
    {
        return view('auth.confirm_password');
    }

    public function reset_password_save(Request $request)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);
        /** @var User $user */
        $user = User::query()->where($request->session()->get('verifications')['key'], $request->session()->get('verifications')['value'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();
        auth()->login($user);
        return redirect('/profile');
    }

    public function verifyProfil(Request $request, User $user)
    {
        /** @var Task $task */
        $task = Task::query()->find($request->get('for_ver_func'));
        $request->validate(
            ['sms_otp' => 'required'],
            ['sms_otp.required' => 'Требуется заполнение!']
        );

        if ($request->get('sms_otp') === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                if ($task->phone === null && $user->phone_number !== $task->phone && (int)$user->is_phone_number_verified === 0) {
                    $user->update(['is_phone_number_verified' => 0]);
                } else {
                    $user->update(['is_phone_number_verified' => 1]);
                }
                if ($task->phone === null) {
                    Task::query()->findOrFail($request->get('for_ver_func'))->update([
                        'status' => 1, 'user_id' => $user->id, 'phone' => $user->phone_number
                    ]);
                } else {
                    Task::query()->findOrFail($request->get('for_ver_func'))->update(['status' => 1, 'user_id' => $user->id,]);

                }
                auth()->login($user);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $request->get('for_ver_func'));
            } else {
                auth()->logout();
                return back()->with('expired_message', __('Срок действия номера истек'));
            }
        } else {
            auth()->logout();
            return back()->with('incorrect_message', __('Ваш номер не подтвержден'));
        }

    }

}
