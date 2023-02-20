<?php

namespace App\Http\Controllers\admin;

use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminPasswordResetRequest;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

class VoyagerUserController extends BaseVoyagerUserController
{
    public function resetPassword(User $user): Factory|View|Application
    {
        return view('vendor.voyager.users.resetPassword',compact('user'));
    }

    public function resetPassword_store(AdminPasswordResetRequest $request, User $user): Redirector|Application|RedirectResponse
    {
        $data = $request->validated();
        unset($data['password_confirmation']);
        $user->update($data);
        $user->password = Hash::make($data['password']);
        $user->updated_password_at = Carbon::now();
        $user->updated_password_by = Auth::id();
        $user->save();
        return redirect('/admin/users');
    }

    public function activity(User $user): RedirectResponse
    {
        if (!auth()->user()->hasPermission("change_activeness")) {
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
