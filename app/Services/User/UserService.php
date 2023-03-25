<?php

namespace App\Services\User;

use App\Mail\VerifyEmail;
use App\Models\{Notification, Session, Task, Transaction, User, WalletBalance};
use App\Services\{CustomService, NotificationService, SmsMobileService};
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\{Auth, Cache, Hash, Mail};
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RealRashid\SweetAlert\Facades\Alert;

class UserService
{
    /**
     * Parolni tiklash uchun telefon raqamga sms kod yuborish
     * @param $phone_number
     * @throws Exception
     */
    public function reset_submit($phone_number): void
    {
        /** @var User $user */
        $user = User::query()->where('phone_number', $phone_number)->first();
        $code = random_int(100000, 999999);
        $user->verify_code = $code;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $message = config('app.name') . ' ' . __("Код подтверждения") . ' ' . $code;
        SmsMobileService::sms_packages((new CustomService)->correctPhoneNumber($phone_number), $message);

        session()->put('verifications', ['key' => 'phone_number', 'value' => $phone_number]);
    }

    /**
     * Parolni tiklash uchun emailga kod yuborish
     * @param $email
     * @throws Exception
     */
    public function reset_by_email($email): void
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
     * Tiklangan parolni saqlash
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
     * Login qilish(api)
     * @param $user
     * @return JsonResponse
     */
    public function login_api_service($user): JsonResponse
    {
        $accessToken = $user->createToken('authToken')->accessToken;

        $expiresAt = now()->addMinutes(2); /* keep online for 2 min */
        Cache::put('user-is-online-' . $user->id, true, $expiresAt);

        /* last seen */
        User::query()->where('id', $user->id)->update(['last_seen' => now()]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => asset('storage/' . $user->avatar),
                'balance' => WalletBalance::query()->where(['user_id' => $user->id])->first()?->balance,
                'phone_number' => (new CustomService)->correctPhoneNumber($user->phone_number),
                'email_verified' => boolval($user->is_email_verified),
                'phone_verified' => boolval($user->is_phone_number_verified),
                'role_id' => $user->role_id,
            ],
            'access_token' => $accessToken,
            'socialpas' => $user->has_password
        ]);
    }

    /**
     * Parolni tiklash uchun telefon raqamga sms kod yuborish(api)
     * @param $phone_number
     * @throws Exception
     */
    public function reset_submit_api($phone_number): void
    {
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $phone_number)->firstOrFail();
        $message = random_int(100000, 999999);
        $user->verify_code = $message;
        $user->verify_expiration = Carbon::now()->addMinutes(5);
        $user->save();
        $message = config('app.name').' '. __("Код подтверждения") . ' ' . $message;
        SmsMobileService::sms_packages((new CustomService)->correctPhoneNumber($user->phone_number), $message);
        session(['phone' => $phone_number]);
    }

    /**
     * O'zgartirilgan parolni saqlash
     * @param $phone_number
     * @param $password
     * @return JsonResponse
     */
    public function reset_save($phone_number, $password): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $phone_number)->firstOrFail();
        $user->password = Hash::make($password);
        $user->save();
        return response()->json([
            'success' => true,
            'message' => __('Пароль был изменен')
        ]);
    }

    /**
     * Yuborilgan kodni tasdiqlash
     * @param $phone_number
     * @param $code
     * @return JsonResponse
     */
    public function reset_code($phone_number, $code): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->where('phone_number', '+' . $phone_number)->firstOrFail();

        if ($code === $user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return response()->json(['success' => true, 'message' => __('Введите новый пароль')]);
            }
            return response()->json(['success' => true, 'message' => __('Срок действия кода истек')]);
        }

        return response()->json(['success' => false, 'message' => __('Код ошибки')]);
    }

    /**
     * Register api
     * @param $data
     * @return JsonResponse
     */
    public function register_api_service($data): JsonResponse
    {
        $data['password'] = Hash::make($data['password']);
        unset($data['password_confirmation']);
        /** @var User $user */
        $user = User::query()->create($data);
        $user->update(['phone_number' => $data['phone_number'] . '_' . $user->id]);
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus',0);
        $wallBal->user_id = $user->id;
        $wallBal->save();
        $user->api_token = Str::random(60);
        $user->remember_token = Str::random(60);
        $user->save();
        Auth::login($user);
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $auth_user = auth()->user();
        return response()->json(['user' => $auth_user, 'access_token' => $accessToken, 'socialpas' => $user->has_password]);
    }

    /**
     * Admin settingdan kiritilgan moderatorni qiymatini qaytaradi
     * @return JsonResponse
     */
    public function getSupportId(): JsonResponse
    {
        /** @var User $user */
        $user = User::query()->findOrFail(setting('site.moderator_id',1));
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => url('/storage') . '/' . $user->avatar,
                'last_seen' => $user->last_seen
            ]
        ]);
    }

    /**
     * logout qilish(api)
     * @param $device_id
     * @return JsonResponse
     */
    public function logout($device_id): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $user->tokens->each(function ($token) {
            $token->delete();
        });

        if ($device_id) {
            Session::query()
                ->where('user_id', $user->id)
                ->where('device_id', $device_id)
                ->delete();
        }
        return response()->json([
            'success' => true,
            'message' => __('Успешно вышел из системы')
        ]);
    }

    /**
     * yuborilgan kodni tasdiqlash(web)
     * @param $verifications
     * @param $code
     * @return RedirectResponse
     */
    public function reset_code_web($verifications, $code): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->where($verifications['key'], $verifications['value'])->first();

        if ((int)$code === (int)$user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                return redirect()->route('user.reset_password');
            }
            return back()->with(['error' => __('Срок действия кода истек')]);
        }

        return back()->with(['error' => __('Код ошибки')]);
    }

    /**
     * Profilni udalit qilish
     * @param $user
     * @param $code
     * @return Redirector|Application|RedirectResponse
     */
    public function confirmationSelfDelete($user, $code): Redirector|Application|RedirectResponse
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

    /**
     * Task create qilishda nomerni verify qilish
     * @param $for_ver_func
     * @param $user
     * @param $sms_otp
     * @return RedirectResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyProfile($for_ver_func, $user, $sms_otp): RedirectResponse
    {
        /** @var Task $task */
        $task = Task::select('phone')->find($for_ver_func);

        if ((int)$sms_otp === (int)$user->verify_code) {
            if (strtotime($user->verify_expiration) >= strtotime(Carbon::now())) {
                if ($task->phone === null && $user->phone_number !== $task->phone && (int)$user->is_phone_number_verified === 0) {
                    $user->update(['is_phone_number_verified' => 0]);
                } else {
                    $user->update(['is_phone_number_verified' => 1]);
                    $user->phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
                    $user->save();
                }
                if ($task->phone === null) {
                    Task::findOrFail($for_ver_func)->update([
                        'status' => 1, 'user_id' => $user->id, 'phone' => (new CustomService)->correctPhoneNumber($user->phone_number)
                    ]);
                } else {
                    Task::findOrFail($for_ver_func)->update(['status' => Task::STATUS_OPEN, 'user_id' => $user->id,]);
                }
                auth()->login($user);

                //send notification
                NotificationService::sendTaskNotification($task, $user->id);

                return redirect()->route('searchTask.task', $for_ver_func);
            }

            auth()->logout();
            return back()->with('expired_message', __('Срок действия номера истек'));
        }

        auth()->logout();
        return back()->with('incorrect_message', __('Ваш номер не подтвержден'));
    }

    /**
     * tranzaksiyalar bo'yicha filter
     * @param $user
     * @param $payment
     * @return JsonResponse
     */
    public function getTransactions($user, $payment): JsonResponse
    {
        if(in_array($payment, Transaction::METHODS, true) ||  $payment === 'task') {
            $transactionMethod = Transaction::query()
                ->where('payment_system', strtolower($_GET['method']))
                ->where(['transactionable_id' => $user->id]);
        } else {
            Alert::error(__('Неопределенный способ оплаты'));
            return response()->json([
                'success' => false,
                'message' => __('Неопределенный способ оплаты')
            ]);
        }

        if (array_key_exists('period', $_GET)){
            $filter = match ($_GET['period']) {
                'month' => now()->subMonth(),
                'week' => now()->subWeek(),
                'year' => now()->subYear(),
                default => now(),
            };
            $transactions = $transactionMethod->where('created_at', '>', $filter)->get();
        } else {
            $from = $_GET['from_date'];
            $to = $_GET['to_date'];
            $transactions = $transactionMethod->where('created_at', '>', $from)
                ->where('created_at', '<', $to)->get();
        }
        $data = [];
        foreach ($transactions as $transaction) {
            $amount = ucfirst($transaction->payment_system) === 'Paynet' ? $transaction->amount / 100 : $transaction->amount;
            $created_at = $transaction->created_at;
            $date = new Carbon($created_at);
            $data[] = ['amount' => $amount, 'created_at' => $date->toDateTimeString()];
        }
        return response()->json([
            'transactions' => $data,
        ]);
    }

    /**
     * Admin tompnidan yaratilgan userga balance bering va push notification jo'natish
     * @param $new_user
     * @return void
     */
    public function new_user($new_user): void
    {
        $wallBal = new WalletBalance();
        $wallBal->balance = setting('admin.bonus',0);
        $wallBal->user_id = $new_user->id;
        $wallBal->save();
        if(setting('admin.bonus',0) > 0){
            Notification::query()->create([
                'user_id' => $new_user->id,
                'description' => 'wallet',
                'type' => Notification::WALLET_BALANCE,
            ]);
        }
    }

    /**
     * @param $user
     * @param $session_id
     * @return mixed
     */
    public function clearSessions($user, $session_id): mixed
    {
        Session::query()->where('user_id', $user->id)->whereNot('id', $session_id)->delete();
        $user->tokens->each(function ($token, $key) use ($user) {
            if ((int)$token->id !== (int)$user->token()->id) {
                $token->delete();
            }
        });
        return response()->json([
            'success' => true,
            'message' => __('Успешно удалено'),
        ]);
    }

    /**
     * Adminkadan user parolini o'zgartirish
     * @param $data
     * @param $user
     * @return Redirector|Application|RedirectResponse
     */
    public function resetPassword_store($data, $user): Redirector|Application|RedirectResponse
    {
        unset($data['password_confirmation']);
        $user->update($data);
        $user->password = Hash::make($data['password']);
        $user->updated_password_at = Carbon::now();
        $user->updated_password_by = Auth::id();
        $user->save();
        return redirect('/admin/users');
    }

    /**
     * Usern adminkadan active yoki nonactive qilish
     * @param $user
     * @param $authUser
     * @return RedirectResponse
     */
    public function activity($user, $authUser): RedirectResponse
    {
        if (!$authUser->hasPermission("change_activeness")) {
            return back()->with([
                'message' => "Sizga ruxsat etilmagan!",
                'alert-type' => 'error',
            ]);
        }
        if ($user->is_active) {
            Session::query()->where('user_id', $user->id)->delete();
            $user->tokens->each(function ($token, $key) {
                $token->delete();
            });
        }
        $user->is_active = $user->is_active ? 0 : 1;
        $user->save();
        return back()->with([
            'message' => "Muvafaqiyatli o'zgartirildi!"
        ]);
    }

}
