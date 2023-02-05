<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AdminPasswordResetRequest;

class VoyagerUserController extends Controller
{
    public function resetPassword(User $user){

        return view('vendor.voyager.users.resetPassword',compact('user'));
    }

    public function resetPassword_store(AdminPasswordResetRequest $request, User $user){

        $data = $request->validated();
        unset($data['password_confirmation']);
        $user->update($data);
        $user->password = Hash::make($data['password']);
        $user->updated_password_at = Carbon::now();
        $user->updated_password_by = Auth::id();
        $user->save();
        return redirect('/admin/users');

    }
}
