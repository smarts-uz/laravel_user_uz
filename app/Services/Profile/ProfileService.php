<?php

namespace App\Services\Profile;

use App\Item\ProfileCashItem;
use App\Item\ProfileDataItem;
use App\Models\Region;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
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

    public function uploadImageServ($request){
        $imgData = session()->has('images') ? json_decode(session('images')):[];
        $files = $request->file('files');
        if ($request->hasFile('files')) {
            foreach ($files as $file) {
                $name = Storage::put('public/uploads', $file);
                $name = str_replace('public/', '', $name);
                array_push($imgData,$name);
            }
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
        $review_good = User::find($user->id)->review_good;
        $review_bad = User::find($user->id)->review_bad;
        $review_rating = User::find($user->id)->review_rating;
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
        if (!str_starts_with($data['phone_number'], '+998')) {
            $data['phone_number'] = '+998' . $data['phone_number'];
        }
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
        $item ->views = $user->views_count;
        $item ->task = $user->tasks_count;
        $item ->ports = $user->portfoliocomments;
        $item ->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::where('role_id', 2)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(20)->pluck('id')->toArray();
        $item ->file = "Portfolio/{$user->name}";
        if (!file_exists($item ->file)) {
            File::makeDirectory($item ->file);
        }
        $item ->b = File::directories(public_path("Portfolio/{$user->name}"));
        $item ->directories = array_map('basename',  $item ->b );
        $item ->categories = Category::withTranslations(['ru', 'uz'])->get();
        $item ->review_good = User::find($user->id)->review_good;
        $item ->review_bad = User::find($user->id)->review_bad;
        $item ->review_rating = User::find($user->id)->review_rating;
        $item ->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->get();
        $item ->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->get();
        return $item;
    }
}
