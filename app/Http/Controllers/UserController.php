<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetCodeRequest;
use App\Http\Requests\ResetEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\VerifyProfileRequest;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Models\Task;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    /**
     * @throws \Exception
     */
    public function reset_submit(ResetRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('phone_number', $data['phone_number'])->first();
        $code = random_int(100000, 999999);
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $message = config('app.name').' '. __("Код подтверждения") . ' ' . $code;
        SmsMobileService::sms_packages(correctPhoneNumber($data['phone_number']), $message);

        session()->put('verifications', ['key' => 'phone_number', 'value' => $data['phone_number']]);

        return redirect()->route('user.reset_code_view');
    }

    /**
     * @throws \Exception
     */
    public function reset_by_email(ResetEmailRequest $request): RedirectResponse
    {

        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where('email', $data['email'])->first();
        $sms_otp = random_int(100000, 999999);
        $message = config('app.name').' ' . __("Код подтверждения") . ' ' . $sms_otp;

        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        session()->put('verifications', ['key' => 'email', 'value' => $data['email']]);

        Mail::to($user->email)->send(new VerifyEmail($message));
        Alert::success(__('Поздравляю'), __('Ваш проверочный код успешно отправлен на') . $user->email);

        return redirect()->route('user.reset_code_view_email');
    }

    public function reset_code(ResetCodeRequest $request)
    {
        $data = $request->validated();
        $verifications = $request->session()->get('verifications');
        /** @var User $user */
        $user = User::query()->where($verifications['key'], $verifications['value'])->first();

        if ($data['code'] === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect()->route('user.reset_password');
            }

            abort(419);
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

    public function reset_password_save(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = User::query()->where($request->session()->get('verifications')['key'], $request->session()->get('verifications')['value'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();
        auth()->login($user);
        return redirect('/profile');
    }

    public function verifyProfile(VerifyProfileRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        /** @var Task $task */
        $task = Task::query()->find($data['for_ver_func']);

        if ($data['sms_otp'] === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                if ($task->phone === null && $user->phone_number !== $task->phone && (int)$user->is_phone_number_verified === 0) {
                    $user->update(['is_phone_number_verified' => 0]);
                } else {
                    $user->update(['is_phone_number_verified' => 1]);
                    $user->phone_number = correctPhoneNumber($user->phone_number);
                    $user->save();
                }
                if ($task->phone === null) {
                    Task::query()->findOrFail($data['for_ver_func'])->update([
                        'status' => 1, 'user_id' => $user->id, 'phone' => correctPhoneNumber($user->phone_number)
                    ]);
                } else {
                    Task::query()->findOrFail($data['for_ver_func'])->update(['status' => Task::STATUS_OPEN, 'user_id' => $user->id,]);
                }
                auth()->login($user);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $data['for_ver_func']);
            }

            auth()->logout();
            return back()->with('expired_message', __('Срок действия номера истек'));
        }

        auth()->logout();
        return back()->with('incorrect_message', __('Ваш номер не подтвержден'));

    }

}
