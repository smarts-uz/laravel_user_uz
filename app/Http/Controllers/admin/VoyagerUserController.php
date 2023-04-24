<?php

namespace App\Http\Controllers\admin;

use App\Models\BlogNew;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\User\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use App\Http\Requests\AdminPasswordResetRequest;
use JetBrains\PhpStorm\ArrayShape;
use RealRashid\SweetAlert\Facades\Alert;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

class VoyagerUserController extends BaseVoyagerUserController
{

    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function resetPassword(User $user): Factory|View|Application
    {
        return view('vendor.voyager.users.resetPassword',compact('user'));
    }

    public function resetPassword_store(AdminPasswordResetRequest $request, User $user): Redirector|Application|RedirectResponse
    {
        $data = $request->validated();
        return $this->userService->resetPassword_store($data, $user);
    }

    public function activity(User $user): RedirectResponse
    {
        $authUser = auth()->user();
        return $this->userService->activity($user, $authUser);
    }

    public function blogNews($newsId): RedirectResponse
    {
        $blogNew = BlogNew::find($newsId);
        NotificationService::sendNotification($blogNew);
        Alert::success(__('Уведомления успешно отправлены'));
        return redirect()->back();
    }
}
