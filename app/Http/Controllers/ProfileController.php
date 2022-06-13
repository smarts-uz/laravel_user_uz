<?php

namespace App\Http\Controllers;

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
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\Profile\ProfileService;

class ProfileController extends Controller
{
    //portfolio
    public $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function setSession(Request $request)
    {
        \session()->put('performer_id_for_task', $request->performer_id);
        return redirect('categories/1');
    }

    public function clear_sessions()
    {
        Session::query()->where('user_id', auth()->user()->id)->delete();
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
        return $uploadImg->uploadImageServ($request);
    }

    public function testBase(Request $request)
    {
        $testBaseS = new ProfileService();
        return $testBaseS->testBaseServ($request);

    }

    public function portfolio(Portfolio $portfolio)
    {
        $user = Auth::user();

        $isDelete = false;
        if ($portfolio->user_id == $user->id) {
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
            'review_bad' =>$item->review_bad,
            'task' => $item->task,
            'review_rating' => $item->review_rating,
            'goodReviews' => $item->goodReviews,
            'badReviews' =>$item->badReviews,
        ]);
    }

    //profile Cash
    public function profileCash()
    {
        $user = Auth::user();
        $service = new ProfileService();
        $item = $service->profileCash( $user);
        return view('profile.cash',
        [
            'balance' => $item->balance,
            'task' => $item->task,
            'top_users' => $item->top_users,
            'transactions' => $item->transactions,
            'user' => $item->user,
            'review_good' => $item->review_good,
            'review_bad' =>$item->review_bad,
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
        Auth::user()->update($updatedData);
        Alert::success(__('lang.setting_alert'));
        return redirect()->route('profile.editData');
    }

    public function destroy()
    {
        auth()->user()->delete();
        return redirect('/');
    }

    //getCategory
    public function getCategory(Request $request)
    {
        $request->validate([
            'category' => 'required'
        ]);
        $user = Auth::user();
        $user->role_id = 2;
        $checkbox = implode(",", $request->get('category'));
        $smsNotification = 0;
        $emailNotification = 0;
        if ($request->get('sms_notification') == 1) {
            $smsNotification = 1;
        }
        if ($request->get('email_notification') == 1) {
            $emailNotification = 1;
        }
        $user->update(['category_id' => $checkbox, 'sms_notification' => $smsNotification, 'email_notification' => $emailNotification]);
        return redirect()->route('profile.profileData');
    }

    public function StoreDistrict(Request $request)
    {
        $request->validate([
            'district' => 'required',
        ]);

        $user = Auth::user();
        $user->district = $request->district;
        $user->save();
        return redirect()->back();
    }

    public function EditDescription(Request $request)
    {
        $profile = new ProfileService();
        $profile->editDescription($request);
        return redirect()->back();

    }


    public function change_password(UserPasswordRequest $request)
    {

        $data = $request->validated();
        if (!$data) {
            return redirect()->route('settings#four');
        }

        $data['password'] = Hash::make($data['password']);
        auth()->user()->update($data);

        Alert::success(__('lang.new_password'));

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
        $user = auth()->user();
        $user->update($data);

        return redirect()->route('profile.verificationPhoto');
    }

    public function verificationPhoto()
    {
        return view('personalinfo.profilephoto');
    }

    public function verificationPhotoStore(Request $request)
    {
        $user = Auth::user();
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
            $imagename = "user-avatar/" . $filename->getClientOriginalName();
            $filename->move(public_path() . '/storage/user-avatar/', $imagename);
            $data['avatar'] = $imagename;
        }
        $user->update($data);
        return redirect()->route('profile.verificationCategory');
    }

    public function verificationCategory()
    {
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->get();
        $categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        return view('personalinfo.personalcategoriya', compact('categories','categories2'));
    }

    public function createPortfolio(PortfolioRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->user()->id;

        $data['image'] = session()->has('images') ? session('images') : '[]';

        session()->forget('images');
        Portfolio::create($data);
        return redirect()->route('profile.profileData');


    }

    public function deleteImage(Request $request, Portfolio $portfolio)
    {
        portfolioGuard($portfolio);
        $image = $request->get('image');
        File::delete(public_path() . '/portfolio/'. $image);
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
        $user = User::find(auth()->user()->id);
        $validator = Validator::make($request->all(), [
            'youtube_link' => 'required|url'
        ]);
        if ($validator->fails()) {
            Alert::error(__('Отправить действующую ссылку на Youtube'));
        }
        $validated = $validator->validated();
        $link = $validated['youtube_link'];
        if (!str_starts_with($link, 'https://www.youtube.com/')) {
            Alert::error(__('Отправить действующую ссылку на Youtube'));
        }
        $user->youtube_link = str_replace('watch?v=','embed/',$request->youtube_link);
        $user->save();
        return redirect()->back();
    }
    public function youtube_link_delete(){
        $user = User::find(auth()->user()->id);
        $user->youtube_link = null;
        $user->save();
        return redirect()->back();
    }
}

