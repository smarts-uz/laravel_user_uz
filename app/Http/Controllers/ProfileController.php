<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\CategoryRequest;
use App\Http\Requests\PersonalInfoRequest;
use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\User\PerformerCreateRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Models\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use \TCG\Voyager\Models\Category;
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

    public function clear_sessions()
    {
        /** @var User $user */
        $user = auth()->user();
        Session::query()->where('user_id', $user->id)->whereNot('id', session()->getId())->delete();
        $user->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return back();
    }


    public function comment(Request $request)
    {
        $profC = new ProfileService();
        return $profC->commentServ($request);

    }

    public function delete(Portfolio $portfolio)
    {
        portfolioGuard($portfolio);

        $portfolio->delete();
        return redirect()->route('profile.profileData');
    }

    public function UploadImage(Request $request)
    {
        $uploadImg = new ProfileService();
        $uploadImg->uploadImageServ($request);
        return true;
    }

    public function testBase(Request $request)
    {
        $testBaseS = new ProfileService();
        return $testBaseS->testBaseServ();

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
        $service = new ProfileService();
        $item = $service->profileData($user);

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
            ]);
    }

    //profile Cash
    public function profileCash()
    {
        $user = Auth::user();
        $service = new ProfileService();
        $item = $service->profileCash($user);
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

    //settings
    public function editData()
    {
        $profile = new ProfileService();
        $data = $profile->settingsEdit();

        return view('profile.settings', $data);
    }

    public function updateData(UserUpdateDataRequest $request)
    {
        $data = $request->validated();
        $profile = new ProfileService();
        $updatedData = $profile->settingsUpdate($data);
        Auth::user()->update((array)$updatedData);
        Alert::success(__('Настройки успешно сохранены'));
        return redirect()->route('profile.editData');
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

        return redirect()->route('profile.profileData');
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'district' => 'required',
        ]);
        /** @var User $user */
        $user = Auth::user();
        $user->district = $request->get('district');
        $user->save();
        return redirect()->back();
    }

    public function editDescription(Request $request)
    {
        $profile = new ProfileService();
        $profile->editDescription($request);
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

    public function verificationContactStore(PersonalInfoRequest $request)
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
        return view('personalinfo.profilephoto');
    }

    public function verificationPhotoStore(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $user->role_id = User::ROLE_PERFORMER;
        if (!$user->avatar) {
            $request->validate([
                'avatar' => 'required|image'
            ]);
        }
        $data = $request->all();
        if ($request->hasFile('avatar')) {
            $destination = 'storage/' . $user->avatar;
            if (File::exists($destination)) {
                File::delete($destination);
            }
            $filename = $request->file('avatar');
            $imageName = "user-avatar/" . $filename->getClientOriginalName();
            $filename->move(public_path() . '/storage/user-avatar/', $imageName);
            $data['avatar'] = $imageName;
        }
        $user->update($data);
        return redirect()->route('profile.verificationCategory');
    }

    public function verificationCategory()
    {
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->orderBy("order", "asc")->get();
        $categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->orderBy("order", "asc")->get();
        return view('personalinfo.personalcategoriya', compact('categories', 'categories2'));
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
        File::delete(public_path() . '/portfolio/' . $image);
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

    public function notif_setting_ajax(Request $request)
    {
        $profile = new ProfileService();
        $profile->userNotifications($request);
        return $request;
    }

    public function storeProfileImage(Request $request)
    {
        $profile = new ProfileService();
        $photoName = $profile->storeProfilePhoto($request);

        if ($photoName) {
            echo json_encode(['status' => 1, 'msg' => 'success', 'name' => $photoName]);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'failed']);
        }
    }

    public function youtube_link(Request $request)
    {
        /** @var User $user */
        $user = User::query()->find(auth()->id());
        $validator = Validator::make($request->all(), [
            'youtube_link' => 'required|url'
        ]);
        if ($validator->fails()) {
            Alert::error(__('Отправить действующую ссылку на Youtube'));
        }
        $validated = $validator->validated();
        $link = $validated['youtube_link'];
        switch (true){
            case str_starts_with($link, 'https://youtu.be/') :
                $user->youtube_link =  str_replace('https://youtu.be', 'https://www.youtube.com/embed', $request->get('youtube_link'));
                $user->save();
                break;
            case str_starts_with($link, 'https://www.youtube.com/') :
                $user->youtube_link = str_replace('watch?v=', 'embed/', $request->get('youtube_link'));
                $user->save();
                break;
            default :
                Alert::error(__('Отправить действующую ссылку на Youtube'));
                break;
        }
        return redirect()->back();
    }

    public function youtube_link_delete()
    {
        /** @var User $user */
        $user = User::query()->find(auth()->id());
        $user->youtube_link = null;
        $user->save();
        return redirect()->back();
    }
}

