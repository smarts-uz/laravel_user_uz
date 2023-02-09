<?php

namespace App\Services\User;

use App\Http\Requests\ResetRequest;
use App\Mail\VerifyEmail;
use App\Models\Task;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\SmsMobileService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse as RedirectResponseAlias;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class UserService
{
    public function reset_submit($phone_number)
    {
        /** @var User $user */
        $user = User::query()->where('phone_number', $phone_number)->first();
        $code = random_int(100000, 999999);
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $message = config('app.name') . ' ' . __("Код подтверждения") . ' ' . $code;
        SmsMobileService::sms_packages(correctPhoneNumber($phone_number), $message);

        session()->put('verifications', ['key' => 'phone_number', 'value' => $phone_number]);
    }

    public function reset_by_email($email)
    {
        /** @var User $user */
        $user = User::query()->where('email', $email)->first();
        $sms_otp = random_int(100000, 999999);
        $message = config('app.name') . ' ' . __("Код подтверждения") . ' ' . $sms_otp;

        $user->verify_code = $sms_otp;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        session()->put('verifications', ['key' => 'email', 'value' => $email]);

        Mail::to($user->email)->send(new VerifyEmail($message));
        Alert::success(__('Поздравляю'), __('Ваш проверочный код успешно отправлен на') . $user->email);
    }

    /**
     * @param $session
     * @param $password
     * @return void
     */
    public function reset_password_save($session, $password): void
    {
        /** @var User $user */
        $user = User::query()->where($session->get('verifications')['key'], $session->get('verifications')['value'])->first();
        $user->password = Hash::make($password);
        $user->save();
    }

    /**
     * @param $for_ver_func
     * @param $user
     * @param $sms_otp
     * @return RedirectResponseAlias
     */
    public function verifyProfile($for_ver_func, $user, $sms_otp): RedirectResponseAlias
    {
        /** @var Task $task */
        $task = Task::query()->find($sms_otp, $for_ver_func);

        if ((int)$sms_otp === (int)$user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                if ($task->phone === null && $user->phone_number !== $task->phone && (int)$user->is_phone_number_verified === 0) {
                    $user->update(['is_phone_number_verified' => 0]);
                } else {
                    $user->update(['is_phone_number_verified' => 1]);
                    $user->phone_number = correctPhoneNumber($user->phone_number);
                    $user->save();
                }
                if ($task->phone === null) {
                    Task::query()->findOrFail($for_ver_func)->update([
                        'status' => 1, 'user_id' => $user->id, 'phone' => correctPhoneNumber($user->phone_number)
                    ]);
                } else {
                    Task::query()->findOrFail($for_ver_func)->update(['status' => Task::STATUS_OPEN, 'user_id' => $user->id,]);
                }
                auth()->login($user);

                // send notification
                NotificationService::sendTaskNotification($task, $user->id);
                return redirect()->route('searchTask.task', $for_ver_func);
            }

            auth()->logout();
            return back()->with('expired_message', __('Срок действия номера истек'));
        }

        auth()->logout();
        return back()->with('incorrect_message', __('Ваш номер не подтвержден'));
    }

    public function confirmationSelfDelete($code, $user)
    {


        if ($code === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                $user->delete();
                return redirect('/');
            }
            return back()->with(['sms_code' => __('Срок действия кода истек')]);
        }

        return back()->with([
            'sms_code' => __('Неправильный код!')
        ]);
    }

    public function self_delete($user)
    {
        /** @var User $user */
        VerificationService::send_verification('phone', $user, $user->phone_number);
        return redirect()->back()->with([
            'sms_code' => __('Код отправлен!')
        ]);
    }

    public function reset_code($request)
    {
        $data = $request->validated();
        $verifications = $request->session()->get('verifications');
        /** @var User $user */
        $user = User::query()->where($verifications['key'], $verifications['value'])->first();

        if ((int)$data['code'] === (int)$user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect()->route('user.reset_password');
            }
            abort(419);
        } else {
            return back()->with(['error' => __('Код ошибки')]);
        }
    }

}
