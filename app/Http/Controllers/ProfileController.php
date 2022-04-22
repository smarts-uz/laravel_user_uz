<?php

namespace App\Http\Controllers;

use App\Http\Requests\PortfolioRequest;
use App\Http\Requests\User\PerformerCreateRequest;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\UserUpdateDataRequest;
use App\Models\Session;
use App\Services\User\Active;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use \TCG\Voyager\Models\Category;
use App\Models\Portfolio;
use App\Models\PortfolioImage;
use App\Models\Region;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Task;
use App\Models\WalletBalance;
use App\Models\All_transaction;
use App\Models\Portfoliocomment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserView;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use App\Services\Profile\ProfileService;
use UAParser\Parser;

class ProfileController extends Controller
{
    //portfolio
    public $agent;

    public function __construct()
    {
        $this->agent = new Agent();

//        if (auth()->check()){

//            $user = Auth::user();
//
//        $user->last_seen_at = Carbon::now()->addMinutes(5);
//            $user->save();
//        }

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

        $views = $user->views_count;
        $task = $user->tasks_count;
        $task_count = $user->performer_tasks()->where('status', 4)->count();
        $ports = $user->portfoliocomments;
        $portfolios = $user->portfolios()->where('image', '!=', null)->get();
        // $image variable hech qatta ishlatilmayaptiku nega kiritgansizlar? Maqsad nima?
        $about = User::where('role_id', 2)->take(20)->get();
        $file = "Portfolio/{$user->name}";
        if (!file_exists($file)) {
            File::makeDirectory($file);
        }
        $b = File::directories(public_path("Portfolio/{$user->name}"));
        $directories = array_map('basename', $b);
        $categories = Category::withTranslations(['ru', 'uz'])->get();
        $goodReviews = $user->goodReviews()->get();
        $badReviews = $user->badReviews()->get();

        return view('profile.profile', compact('categories', 'about', 'portfolios', 'directories', 'task_count', 'user', 'views', 'task', 'ports','goodReviews','badReviews'));
    }

    public function updates(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image'
        ]);
        $user = Auth::user();
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
        return redirect()->back();
    }

    //profile Cash
    public function profileCash()
    {
        $user = Auth()->user()->load('transactions');

        $balance = $user->walletBalance;
        $views = $user->views()->count();
        $task = $user->tasks()->count();
        $transactions = $user->transactions()->paginate(15);
        $about = User::where('role_id', 2)->orderBy('reviews', 'desc')->take(20)->get();
        $task_count = Task::where('performer_id', $user->id)->count();

        return view('profile.cash', compact('user', 'views', 'balance', 'task', 'about', 'task_count', 'transactions'));
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
        Alert::success(__('Настройки успешно сохранены'));
        return redirect()->route('profile.editData');
    }

    public function destroy($id)
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
        $user->update(['category_id' => $checkbox]);
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

        Alert::success("Success!", "Your Password was successfully updated");

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

    public function verificationContactStore(Request $request)
    {
        $data = $request->validate([
            'email' => 'required',
            'phone_number' => 'required|integer|min:9',
        ]);
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
        return view('personalinfo.personalcategoriya', compact('categories'));
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
}

