<?php

namespace App\Services\Profile;

use App\Http\Controllers\ClickuzController;
use App\Http\Controllers\PaynetController;
use App\Item\ProfileCashItem;
use App\Item\ProfileDataItem;
use App\Models\All_transaction;
use App\Models\Region;
use App\Models\Review;
use App\Models\Session;
use App\Models\User;
use App\Models\WalletBalance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
    public function commentServ($request){
        $user = Auth::user();
        $comment = $request->input('comment');
        $description = $request->input('description');
        $data['user_id'] = $user->id;
        $data['comment'] = $comment;
        $data['description'] = $description;
        $dd = Portfolio::create($data);
        return $dd;
    }

    public function uploadImageServ(Request $request)
    {
        $user = auth()->user();
        $imgData = session()->has('images') ? json_decode(session('images')):[];
        foreach ($request->file('images') as $uploadedImage) {
            $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path() . '/portfolio/' . $user->name . '/', $filename);
            $imgData[] = $filename;
        }
        session()->put('images', json_encode($imgData));
    }

    public function testBaseServ($request){
        $user = Auth::user();
        $comment = $user->portfolios()->orderBy('created_at', 'desc')->first();
        $image = File::allFiles("Portfolio/{$user->name}/{$comment->comment}");
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

    public function settingsEdit() {
        $user = Auth::user();
        $views = $user->views()->count();
        $categories = Category::withTranslations(['ru', 'uz'])->where('parent_id', null)->select('id','name')->get();
        $categories2 = Category::where('parent_id','<>', null)->select('id','parent_id','name')->get();
        $regions = Region::withTranslations(['ru', 'uz'])->get();
        $top_users = User::where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $sessions = Session::query()->where('user_id', $user->id)->get();
        $parser = Parser::create();
        $review_good = $user->review_good;
        $review_bad = $user->review_bad;
        $review_rating = $user->review_rating;
        return array(
            'user' => $user,
            'views' => $views,
            'categories' => $categories,
            'categories2' => $categories2,
            'regions' => $regions,
            'top_users' => $top_users,
            'sessions' => $sessions,
            'parser' => $parser,
            'review_good' => $review_good,
            'review_bad' => $review_bad,
            'review_rating' => $review_rating,
        );
    }

    public function settingsUpdate($data) {
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

    public function editDescription(Request $request)
    {
        $user = Auth::user();
        $user->description = $request->description;
        $user->save();
    }

    public function userNotifications(Request $request)
    {
        $user = auth()->user();
        $user->system_notification = $request->notif11;
        $user->news_notification = $request->notif22;
        $user->save();
    }

    public function profileCash($user){
        $item = new ProfileCashItem();
        $item ->user = Auth()->user()->load('transactions');
        $item ->balance =  $item ->user->walletBalance;
        $item ->views =  $item ->user->views()->count();
        $item ->task =  $item ->user->tasks()->count();
        $item ->transactions =  $item ->user->transactions()->paginate(15);
        $item->top_users = User::where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')
        ->limit(20)->pluck('id')->toArray();
        $item ->review_rating = User::find($user->id)->review_rating;
        $item ->review_good = User::find($user->id)->review_good;
        $item ->review_bad = User::find($user->id)->review_bad;
        return $item;
    }
    public function profileData($user){
        $item = new ProfileDataItem();
        $item->views = $user->views_count;
        $item->task = $user->tasks_count;
        $item->ports = $user->portfoliocomments;
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $item->file = "portfolio/{$user->name}";
        if (!file_exists($item->file)) {
            File::makeDirectory($item->file);
        }
        $item->b = File::directories(public_path("portfolio/{$user->name}"));
        $item->directories = array_map('basename',  $item ->b );
        $item->categories = Category::withTranslations(['ru', 'uz'])->get();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->get();
        return $item;
    }

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
        return $reviews->get();
    }

    public function createPortfolio($request)
    {
        $user = auth()->user();
        $data = $request->except('images');
        $data['user_id'] = $user->id;
        if ($request->hasFile('images')) {
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name.'/'.time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path().'/portfolio/'.$user->name.'/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio = Portfolio::create($data);
        return $portfolio;
    }

    public function updatePortfolio($request, $portfolio)
    {
        $user = auth()->user();
        $data = $request->except('images');
        $data['user_id'] = $user->id;
        if ($request->hasFile('images')) {
            $portfolioImages = json_decode($portfolio->image);
            foreach ($portfolioImages as $portfolioImage) {
                File::delete(public_path() . '/portfolio/'. $portfolioImage);
            }
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name.'/'.time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/portfolio/' . $user->name.'/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio->update($data);
        $portfolio->save();
        return $portfolio;
    }

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

    public function balance($request)
    {
        $user = auth()->user()->load('transactions');
        if (WalletBalance::query()->where('user_id', $user->id)->first() != null)
            $balance = WalletBalance::query()->where('user_id', $user->id)->first()->balance;
        else
            $balance = 0;
        $transactions = All_transaction::query()->where(['user_id' => $user->id]);
        $period = $request->get('period');
        $type = $request->get('type');
        if ($type == 'in') {
            $transactions = $transactions->whereIn('method', ['Payme', 'Click', 'Paynet']);
        } elseif ($type == 'out') {
            $transactions = $transactions->where('method', '=', 'Task');
        }
        if ($period == 'month') {
            $transactions = $transactions->where('created_at', '>', Carbon::now()->subMonth()->toDateTimeString());
        }
        return [
            'balance' => $balance,
            'transactions' => $transactions->paginate(15)
        ];
    }

    public function phoneUpdate($request)
    {
        $phoneNumber = $request->get('phone_number');
        $userPhone = User::query()->where(['phone_number' => $phoneNumber])->first();
        $user = auth()->user();
        if ($userPhone) {
            if ($userPhone->id != $user->id) {
                $message = trans('trans.User with entered phone number already exists.');
                $success = false;
            }
        } else {
            $user->phone_number = $phoneNumber;
            $user->is_phone_number_verified = 0;
            $user->save();
            $message = trans('trans.Phone number updated successfully.');
            $success = true;
        }
        return [
            'success' => $success,
            'data' => [
                'message' => $message
            ]
        ];
    }

    public function payment($request)
    {
        $payment = $request->get("paymethod");
        $request['user_id'] = auth()->user()->id;
        switch($payment) {
            case 'Click':
                $payment = new ClickuzController();
                return $payment->pay($request);
            case 'PayMe':
                $tr = new All_transaction();
                $tr->user_id = Auth::id();
                $tr->amount = $request->get("amount");
                $tr->method = $tr::DRIVER_PAYME;
                $tr->state = $tr::STATE_WAITING_PAY;
                $tr->save();
                return redirect('https://checkout.paycom.uz')->withInput([
                    'merchant' => config('paycom.merchant_id'),
                    'amount' => $tr->amount * 100,
                    'order_id' => $tr->id
                ]);
            case 'Paynet':
                return PaynetController::pay($request);
        }
    }

    public function changePassword($request)
    {
        $data = $request->validated();
        $user = auth()->user();
        if (Hash::check($data['old_password'], $user->password)) {
            $user->update(['password' => Hash::make($data['password'])]);

            $message = trans('trans.Password updated successfully.');
            $status = true;
        } else {
            $message = trans('trans.Incorrect old password.');
            $status = false;
        }
        return response()->json([
            'status' => $status,
            'data' => [
                'message' => $message
            ]
        ]);
    }

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
        $checkbox = implode(",", $categories);
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
