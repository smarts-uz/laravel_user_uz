<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\CategoryRequest;
use App\Http\Requests\PersonalInfoRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\User\PerformerCreateRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Models\Session;
use App\Models\UserCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use TCG\Voyager\Models\Category;
use App\Models\Portfolio;
use Illuminate\Support\Facades\File;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\Profile\ProfileService;

class ProfileController extends Controller
{
    public Agent $agent;
    protected ProfileService $profileService;


    public function __construct()
    {
        $this->agent = new Agent();
        $this->profileService = new ProfileService();
    }

    public function setSession(Request $request)
    {
        \session()->put('performer_id_for_task', $request->get('performer_id'));
        return redirect('categories/1');
    }

    public function clear_sessions(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        Session::query()->where('user_id', $user->id)->whereNot('id', session()->getId())->delete();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return back();
    }

    public function delete(Portfolio $portfolio): RedirectResponse
    {
        portfolioGuard($portfolio);

        $portfolio->delete();
        return redirect()->route('profile.profileData');
    }

    public function uploadImage(Request $request): bool
    {
        /** @var User $user */
        $user = auth()->user();
        $uploadedImages = $request->file('images');
        $this->profileService->uploadImageServ($uploadedImages,$user);
        return true;
    }

    public function portfolio(Portfolio $portfolio)
    {
        /** @var User $user */
        $user = Auth::user();

        $isDelete = false;
        if ($portfolio->user_id === $user->id) {
            $isDelete = true;
        }
        return view('profile/portfolio', compact('user', 'portfolio', 'isDelete'));
    }

    //profile
    public function profileData()
    {
        $user = Auth::user();
        $item = $this->profileService->profileData($user);

        return view('profile.profile',
            [
                'categories' => $item->categories,
                'top_users' => $item->top_users,
                'user' => $user,
                'portfolios' => $item->portfolios,
                'review_good' => $item->review_good,
                'review_bad' => $item->review_bad,
                'task' => $item->task,
                'review_rating' => $item->review_rating,
                'goodReviews' => $item->goodReviews,
                'badReviews' => $item->badReviews,
                'user_category'=>$item->user_category
            ]);
    }

    //profile Cash
    public function profileCash()
    {
        $user = Auth::user();
        $item = $this->profileService->profileCash($user);
        return view('profile.cash',
            [
                'balance' => $item->balance,
                'task' => $item->task,
                'top_users' => $item->top_users,
                'transactions' => $item->transactions,
                'user' => $item->user,
                'review_good' => $item->review_good,
                'review_bad' => $item->review_bad,
                'review_rating' => $item->review_rating,
            ]);
    }

    public function editData()
    {
        /** @var User $user */
        $user = Auth::user();
        $item = $this->profileService->settingsEdit($user);

        return view('profile.settings',[
            'user' => $user,
            'categories' => $item->categories,
            'categories2' => $item->categories2,
            'regions' => $item->regions,
            'top_users' => $item->top_users,
            'sessions' => $item->sessions,
            'parser' => $item->parser,
            'review_good' => $item->review_good,
            'review_bad' => $item->review_bad,
            'review_rating' => $item->review_rating,
            'task' => $item->task,
            'user_categories' => $item->user_categories,
        ]);
    }

    public function updateData(UserUpdateDataRequest $request): RedirectResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $updatedData = $this->profileService->settingsUpdate($data, $user);
        Auth::user()->update((array)$updatedData);
        Alert::success(__('Настройки успешно сохранены'));
        return redirect()->back();
    }

    public function destroy()
    {
        auth()->user()->delete();
        return redirect('/');
    }

    //getCategory
    public function getCategory(CategoryRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $categories = $data['category'];

        $sms_notification = (int)$request->get('sms_notification');
        $email_notification = (int)$request->get('email_notification');

        $this->profileService->subscribeToCategory($categories, $user, $sms_notification, $email_notification);
        Alert::success(__('Настройки успешно сохранены'));
        return redirect()->back();
    }

    public function editDescription(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $user->description = $request->get('description');
        $user->save();
        return redirect()->back();

    }


    public function change_password(UserPasswordRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        switch (true){
            case !$data || (!isset($data['old_password']) && $user->password) :
                Alert::error(__('Введите старый пароль'));
                return redirect()->back();
            case isset($data['old_password']) && !Hash::check($data['old_password'], $user->password) :
                Alert::error(__('Неверный старый пароль'));
                return redirect()->back();
        }

        $data['password'] = Hash::make($data['password']);
        unset($data['old_password']);
        $user->update($data);

        Alert::success(__('Ваш пароль был успешно обновлен'));

        return redirect()->back()->with([
            'password' => 'password'
        ]);
    }

    //personal info Ijrochi uchun

    public function verificationIndex()
    {
        return view('verification.verification');
    }

    public function verificationInfo()
    {
        return view('personalinfo.personalinfo');
    }

    public function verificationInfoStore(PerformerCreateRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $user->update($data);
        return redirect()->route('profile.verificationContact');
    }

    public function verificationContact()
    {
        return view('personalinfo.contact');
    }

    public function verificationContactStore(PersonalInfoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        /** @var User $user */
        $user = auth()->user();
        $user->update($data);
        if((int)$user->is_phone_number_verified !== 1){
            $user->phone_number = $data['phone_number'] . '_' . $user->id;
            $user->save();
        }

        return redirect()->route('profile.verificationPhoto');
    }

    public function verificationPhoto()
    {
        /** @var User $user */
        $user = auth()->user();
        return view('personalinfo.profilephoto',compact('user'));
    }

    public function verificationPhotoStore(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $this->profileService->storeProfilePhoto($request, $user);

        return redirect()->route('profile.verificationCategory');
    }

    public function verificationCategory()
    {
        /** @var User $user */
        $user = Auth::user();
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->orderBy("order", "asc")->get();
        $categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        $user_categories = UserCategory::query()->where('user_id',$user->id)->pluck('category_id')->toArray();
        return view('personalinfo.personalcategoriya', compact('categories', 'categories2','user_categories'));
    }

    public function createPortfolio(PortfolioRequest $request,Portfolio $portfolio)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['image'] = session()->has('images') ? session('images') : '[]';

        session()->forget('images');
        $portfolio->create($data);
        return redirect()->route('profile.profileData');
    }

    public function deleteImage(Request $request, Portfolio $portfolio)
    {
        portfolioGuard($portfolio);
        $image = $request->get('image');
        File::delete(public_path() . '/storage/portfolio/' . $image);
        $images = json_decode($portfolio->image);
        $updatedImages = array_diff($images, [$image]);
        $portfolio->image = json_encode(array_values($updatedImages));
        $portfolio->save();
        return true;
    }

    public function updatePortfolio(PortfolioRequest $request, Portfolio $portfolio)
    {
        portfolioGuard($portfolio);
        $data = $request->validated();

        $images = array_merge(json_decode(session()->has('images') ? session('images') : '[]'), json_decode($portfolio->image));

        session()->forget('images');
        $data['image'] = json_encode($images);
        $portfolio->update($data);
        $portfolio->save();
        return redirect()->route('profile.profileData');
    }

    public function notif_setting_ajax(Request $request): Request
    {
        /** @var User $user */
        $user = auth()->user();
        $user->system_notification = $request->get('notif11');
        $user->news_notification = $request->get('notif22');
        $user->save();
        return $request;
    }

    public function storeProfileImage(Request $request): void
    {
        /** @var User $user */
        $user = auth()->user();
        $photoName = $this->profileService->storeProfilePhoto($request, $user);

        if ($photoName) {
            echo json_encode(['status' => 1, 'msg' => 'success', 'name' => $photoName]);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'failed']);
        }
    }

    public function youtube_link(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();
        $validated = $request->validate([
            'youtube_link' => 'required|url'
        ]);
        $link = $validated['youtube_link'];
        $response = $this->profileService->videoStore($user, $link);
        return redirect()->back()->with('message', $response['message']);
    }

    public function youtube_link_delete(): RedirectResponse
    {
        /** @var User $user */
        $user = User::query()->find(auth()->id());
        $user->youtube_link = null;
        $user->save();
        return redirect()->back();
    }
}

