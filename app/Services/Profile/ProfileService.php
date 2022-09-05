<?php

namespace App\Services\Profile;

use App\Http\Resources\TransactionHistoryCollection;
use App\Http\Resources\TransactionHistoryResource;
use App\Item\ProfileCashItem;
use App\Item\ProfileDataItem;
use App\Models\All_transaction;
use App\Models\Region;
use App\Models\Review;
use App\Models\Session;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\PaymentService;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use TCG\Voyager\Models\Category;
use UAParser\Parser;


class ProfileService
{
    /**
     *
     * Function  commentServ
     * Mazkur metod user portfolioda qoldirilgan commentni saqlaydi
     * @param $request Object
     */
    public function commentServ($request)
    {
        $user = Auth::user();
        $comment = $request->input('comment');
        $description = $request->input('description');
        $data['user_id'] = $user->id;
        $data['comment'] = $comment;
        $data['description'] = $description;
        $dd = Portfolio::create($data);
        return $dd;
    }

    /**
     *
     * Function  uploadImageServ
     * Mazkur metod user portfolioda rasmlarni saqlaydi
     * @param Request $request Object
     */
    public function uploadImageServ(Request $request)
    {
        $user = auth()->user();
        $imgData = session()->has('images') ? json_decode(session('images')) : [];
        foreach ($request->file('images') as $uploadedImage) {
            $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path() . '/portfolio/' . $user->name . '/', $filename);
            $imgData[] = $filename;
        }
        session()->put('images', json_encode($imgData));
    }

    /**
     *
     * Function  testBaseServ
     * Mazkur metod user portfolioni tahrirlaydi
     * @param $request Object
     */
    public function testBaseServ($request)
    {
        $user = Auth::user();
        $comment = $user->portfolios()->orderBy('created_at', 'desc')->first();
        $image = File::allFiles("portfolio/{$user->name}");
        $json = implode(',', $image);
        $data['image'] = $json;
        $id = $comment->id;
        $base = new Portfolio();
        if ($base->where('id', $id)->update($data)) {
            return redirect()->route('profile.profileData');
        } else {
            return dd(false);
        }
    }

    /**
     *
     * Function  settingsEdit
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni chiqarib beradi
     * @param setting Object
     */
    public function settingsEdit()
    {
        $user = Auth::user();
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->select('id', 'name')->get();
        $categories2 = Category::where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->get();
        $regions = Region::withTranslations(['ru', 'uz'])->get();
        $top_users = User::where('role_id', 2)->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $sessions = Session::query()->where('user_id', $user->id)->get();
        $parser = Parser::create();
        $review_good = $user->review_good;
        $review_bad = $user->review_bad;
        $review_rating = $user->review_rating;
        $task = Task::where('user_id', Auth::id())->whereIn('status', [1,2,3,4,5,6])->get();
        return array(
            'user' => $user,
            'categories' => $categories,
            'categories2' => $categories2,
            'regions' => $regions,
            'top_users' => $top_users,
            'sessions' => $sessions,
            'parser' => $parser,
            'review_good' => $review_good,
            'review_bad' => $review_bad,
            'review_rating' => $review_rating,
            'task'=>$task
        );
    }

    /**
     *
     * Function  settingsUpdate
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni tahrirlaydi
     * @param $data Object
     */
    public function settingsUpdate($data)
    {
        if ($data['email'] != auth()->user()->email) {
            $data['is_email_verified'] = 0;
            $data['email_old'] = auth()->user()->email;
        }
        if ($data['phone_number'] != auth()->user()->phone_number) {
            $data['is_phone_number_verified'] = 0;
            $data['phone_number_old'] = auth()->user()->phone_number;
        }
        return $data;
    }

    /**
     *
     * Function  storeProfilePhoto
     * Mazkur metod user profilidagi rasmni tahrirlaydi
     * @param Request $request Object
     */
    public function storeProfilePhoto(Request $request)
    {
        if ($request->hasFile('image')) {

            $files = $request->file('image');
            $name = Storage::put('public/uploads', $files);
            $name = str_replace('public/', '', $name);
            $user = auth()->user();
            $user->avatar = $name;
            $user->save();
            return $name;
        }
        return null;
    }

    /**
     *
     * Function  editDescription
     * Mazkur metod user qoldirgan tavsifni tahrirlayi
     * @param Request $request Object
     */
    public function editDescription(Request $request)
    {
        $user = Auth::user();
        $user->description = $request->description;
        $user->save();
    }

    /**
     *
     * Function  userNotifications
     * Mazkur metod setting bo'limidagi system va news notification
     * @param Request $request Object
     */
    public function userNotifications(Request $request)
    {
        $user = auth()->user();
        $user->system_notification = $request->notif11;
        $user->news_notification = $request->notif22;
        $user->save();
    }

    /**
     *
     * Function  profileCash
     * Mazkur metod profile cash bo'limini ochib beradi
     * @param $user Object
     */
    public function profileCash($user)
    {
        $item = new ProfileCashItem();
        $item->user = Auth()->user()->load('transactions');
        $item->balance = $item->user->walletBalance;
        $item->task = Task::where('user_id', Auth::id())->whereIn('status', [1,2,3,4,5,6])->get();
        $item->transactions = $item->user->transactions()->paginate(15);
        $item->top_users = User::where('role_id', 2)->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $item->review_rating = $user->review_rating;
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        return $item;
    }

    /**
     *
     * Function  profileData
     * Mazkur metod profile  bo'limini ochib beradi
     * @param $user Object
     */
    public function profileData($user)
    {
        $item = new ProfileDataItem();
        $item->task = Task::where('user_id', Auth::id())->whereIn('status', [1,2,3,4,5,6])->get();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::where('role_id', 2)->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $item->categories = Category::withTranslations(['ru', 'uz'])->get();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->get();
        return $item;
    }

    /**
     *
     * Function  userReviews
     * Mazkur metod userga qoldirilgan reviewlar chiqarib beradi
     * @param Request $request Object
     */
    public static function userReviews($user, Request $request)
    {
        $reviews = Review::query()->whereHas('task')->where(['user_id' => $user->id]);
        $performer = $request->get('performer');
        if (isset($performer)) {
            $reviews->where(['as_performer' => $performer]);
        }
        if ($request->get('review') == 'good') {
            $reviews->where(['good_bad' => 1]);
        } elseif ($request->get('review') == 'bad') {
            $reviews->where(['good_bad' => 0]);
        }
        return $reviews->orderByDesc('created_at')->get();
    }

    /**
     *
     * Function  createPortfolio
     * Mazkur metod portfolio tablega rasmlarni saqlash
     * @param Request $request Object
     */
    public function createPortfolio($request)
    {
        $user = auth()->user();
        $data = $request->except('images');
        $data['user_id'] = $user->id;
        if ($request->hasFile('images')) {
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/portfolio/' . $user->name . '/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio = Portfolio::create($data);
        return $portfolio;
    }

    /**
     *
     * Function  updatePortfolio
     * Mazkur metod portfolio rasmlarni tahrirlash
     * @param $portfolio Object
     */
    public function updatePortfolio($request, $portfolio)
    {
        $user = $portfolio->user;
        $imgData = $portfolio->image ? json_decode($portfolio->image) : [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/portfolio/' . $user->name . '/', $filename);
                $imgData[] = $filename;
            }
        }
        $data['comment'] = $request->get('comment');
        $data['description'] = $request->get('description');
        $data['image'] = json_encode($imgData);
        $portfolio->update($data);
        $portfolio->save();

        return $portfolio;
    }

    /**
     *
     * Function  videoStore
     * Mazkur metod profilga video saqlash
     * @param $request Object
     */
    public function videoStore($request)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $link = $validated['link'];
        if (!str_starts_with($link, 'https://www.youtube.com/')) {
            $message = trans('trans.Link should be from YouTube.');
            $success = false;
        } else {
            $user->youtube_link = str_replace('watch?v=', 'embed/', $link);
            $user->save();
            $message = trans('trans.Video added successfully.');
            $success = true;
        }
        return [
            'success' => $success,
            'data' => [
                'message' => $message
            ]
        ];
    }

    /**
     *
     * Function  balance
     * Mazkur metod cash bladega balansni chiqaradi
     * @param $request Object
     */
    public function balance($request)
    {
        $user = auth()->user();
        if (WalletBalance::query()->where('user_id', $user->id)->first() != null)
            $balance = WalletBalance::query()->where('user_id', $user->id)->first()->balance;
        else
            $balance = 0;
        $transactions = Transaction::query()->where(['transactionable_id' => $user->id])->where('state', 2);
        $period = $request->get('period');
        $from = $request->get('from');
        $to = $request->get('to');
        $type = $request->get('type');
        if ($type == 'in') {
            $transactions = $transactions->whereIn('payment_system', Transaction::METHODS);
        } elseif ($type == 'out') {
            $transactions = $transactions->where('payment_system', '=', 'Task');
        }
        $now = Carbon::now();
        if ($period) {
            $transactions = match ($period) {
                'month' => $transactions->where('created_at', '>', $now->subMonth()),
                'week' => $transactions->where('created_at', '>', $now->subWeek()),
                'year' => $transactions->where('created_at', '>', $now->subYear()),
            };
        } elseif ($from && $to) {
            $transactions = $transactions->where('created_at', '>', $from)
                ->where('created_at', '<', $to);
        }
        return [
            'balance' => $balance,
            'transaction' => TransactionHistoryCollection::collection($transactions->orderByDesc('created_at')->paginate(15))->response()->getData(true)
//            'transactions' => new TransactionHistoryResource($transactions->orderByDesc('created_at')->paginate(15))
        ];
    }

    /**
     *
     * Function  phoneUpdate
     * Mazkur metod telefon raqamni tahrirlaydi
     * @param $request Object
     */
    public function phoneUpdate($request)
    {
        $phoneNumber = $request->get('phone_number');
        /** @var User $userPhone */
        $userPhone = User::query()->where(['phone_number' => $phoneNumber])->first();
        /** @var User $user */
        $user = auth()->user();
        if ($userPhone && ($userPhone->id != $user->id)) {
            $messages = trans('trans.User with entered phone number already exists.');
            $success = false;
        } else {
            $user->phone_number_old = $user->phone_number;
            $user->phone_number = $phoneNumber;
            $user->is_phone_number_verified = 0;
            $message = rand(100000, 999999);
            $phone_number = $user->phone_number;
            $user->verify_code = $message;
            $user->save();
            $sms_service = new SmsMobileService();
            $sms_service->sms_packages($phone_number, "User.Uz ". __("Код подтверждения") . ' ' . $message);
            $messages = trans('trans.Phone number updated successfully.');
            $success = true;
        }
        return [
            'success' => $success,
            'data' => [
                'messages' => $messages 
            ]
        ];
    }

    /**
     *
     * Function  changePassword
     * Mazkur metod passwordni tahrirlash
     * @param $data Object
     */
    public function changePassword($data)
    {
        $user = auth()->user();
        if (isset($user->password)) {
            if (Hash::check($data['old_password'], $user->password)) {
                $user->update(['password' => Hash::make($data['password'])]);

                $message = trans('trans.Password updated successfully.');
                $status = true;
            } else {
                $message = trans('trans.Incorrect old password.');
                $status = false;
            }
        } else {
            $user->update(['password' => Hash::make($data['password'])]);

            $message = trans('trans.Password updated successfully.');
            $status = true;
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => []
        ]);
    }

    /**
     *
     * Function  changeAvatar
     * Mazkur metod profilda rasm tahrirlash
     * @param $request Object
     */
    public function changeAvatar($request)
    {
        $user = auth()->user();
        $destination = 'storage/' . $user->avatar;
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $filename = $request->file('avatar');
        $imagename = "user-avatar/" . $filename->getClientOriginalName();
        $filename->move(public_path() . '/storage/user-avatar/', $imagename);
        $data['avatar'] = $imagename;
        $user->update($data);
    }

    /**
     *
     * Function  updateSettings
     * Mazkur metod settingni tahrirlash
     * @param $request Object
     */
    public function updateSettings($request)
    {
        $validated = $request->validated();
        if ($validated['email'] != auth()->user()->email) {
            $validated['is_email_verified'] = 0;
            $validated['email_old'] = auth()->user()->email;
        }
        $user = auth()->user();
        $user->update($validated);
        $user->save();
    }

    /**
     *
     * Function  notifications
     * Mazkur metod setting notification api
     * @param $request Object
     */
    public function notifications($request)
    {
        $notification = $request->get('notification');
        $user = auth()->user();
        if ($notification == 1) {
            $user->system_notification = 1;
            $user->news_notification = 1;
            $message = trans('trans.Notifications turned on.');
        } elseif ($notification == 0) {
            $user->system_notification = 0;
            $user->news_notification = 0;
            $message = trans('trans.Notifications turned off.');
        }
        $user->save();
        return $message;
    }

    /**
     *
     * Function  subscribeToCategory
     * Mazkur metod setting categorylarni tahrirlash
     * @param $request Object
     */
    public function subscribeToCategory($request)
    {
        $user = auth()->user();
        $categories = $request->get('category');
        foreach ($categories as $category) {
            if (!is_int($category)) {
                return [
                    'success' => false,
                    'data' => [
                        'message' => trans('trans.All values should be int.')
                    ]
                ];
            }
        }
        $parentCategories = \App\Models\Category::with('childs')->where('parent_id', null)->whereIn('id', $categories)->get();
        $childCategories = $parentCategories->pluck('childs')->flatten()->pluck('id')->toArray();
        $withoutParents = array_diff($categories, $parentCategories->pluck('id')->toArray());
        $allChildCategories = array_unique(array_merge($childCategories, $withoutParents));
        $checkbox = implode(",", $allChildCategories);
        $smsNotification = 0;
        $emailNotification = 0;
        if ($request->get('sms_notification') == 1) {
            $smsNotification = 1;
        }
        if ($request->get('email_notification') == 1) {
            $emailNotification = 1;
        }
        $user->update(['category_id' => $checkbox, 'sms_notification' => $smsNotification, 'email_notification' => $emailNotification]);
        return [
            'success' => true,
            'data' => [
                'message' => trans('trans.You successfully subscribed for notifications by task categories.')
            ]
        ];
    }
}
