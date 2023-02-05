<?php

namespace App\Services\Profile;

use App\Http\Resources\TransactionHistoryCollection;
use App\Item\ProfileCashItem;
use App\Item\ProfileDataItem;
use App\Item\ProfileSettingItem;
use App\Item\VerificationCategoryItem;
use App\Models\Region;
use App\Models\Review;
use App\Models\Session;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserCategory;
use App\Models\WalletBalance;
use App\Services\SmsMobileService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use JetBrains\PhpStorm\ArrayShape;
use App\Models\Category;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;


class ProfileService
{
    public const MAX_TRANSACTIONS = 15;

    /**
     *
     * Function  uploadImageServ
     * Mazkur metod user portfolioda rasmlarni saqlaydi
     * @param $uploadedImages
     * @param $user
     */
    public function uploadImageServ($uploadedImages, $user): void
    {
        $imgData = session()->has('images') ? json_decode(session('images')) : [];
        foreach ($uploadedImages as $uploadedImage) {
            $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
            $uploadedImage->move(public_path() . '/storage/portfolio/' . $user->name . '/', $filename);
            $imgData[] = $filename;
        }
        session()->put('images', json_encode($imgData));
    }

    /**
     *
     * Function  settingsEdit
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni chiqarib beradi
     * @param $user
     * @return ProfileSettingItem
     * @throws FileNotFoundException
     */
    public function settingsEdit($user): ProfileSettingItem
    {
        $item = new ProfileSettingItem();
        $item->categories = Category::query()->where('parent_id', null)->select('id', 'name')->orderBy("order")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->orderBy("order")->get();
        $item->regions = Region::all();
        $item->top_users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')
            ->toArray();
        $item->sessions = Session::query()->where('user_id', $user->id)->get();
        $item->parser = Parser::create();
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        $item->review_rating = $user->review_rating;
        $item->user_categories = UserCategory::query()->where('user_id',$user->id)->pluck('category_id')->toArray();
        $item->task = Task::query()->where('user_id', Auth::id())->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        return $item;
    }

    /**
     *
     * Function  settingsUpdate
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni tahrirlaydi
     * @param $data
     * @param $user
     * @return mixed
     */
    public function settingsUpdate($data, $user)
    {
        if ($data['email'] !== $user->email) {
            $data['is_email_verified'] = 0;
            $data['email_old'] = $user->email;
        }
        if ($data['phone_number'] !== $user->phone_number) {
            $data['is_phone_number_verified'] = 0;
            $data['phone_number_old'] = $user->phone_number;
        }
        return $data;
    }

    /**
     *
     * Function  storeProfilePhoto
     * Mazkur metod user profilidagi rasmni tahrirlaydi
     * @param $data
     * @param $user
     * @return string|null
     */
    public function storeProfilePhoto($data, $user): ?string
    {
        if ($data->hasFile('image')) {
            $files = $data->file('image');
            $filename = 'user-avatar/'.$files->getClientOriginalName().'_'.time() . ".jpg";
            $files->move(public_path().'/storage/user-avatar/', $filename);
            $user->avatar = $filename;
            $user->save();
            return $filename;
        }
        return null;
    }

    /**
     *
     * Function  profileCash
     * Mazkur metod profile cash bo'limini ochib beradi
     * @param $user
     * @return ProfileCashItem
     */
    public function profileCash($user): ProfileCashItem
    {
        $item = new ProfileCashItem();
        $item->user = Auth()->user()->load('transactions');
        $item->balance = $item->user->walletBalance;
        $item->task = Task::query()->where('user_id', Auth::id())->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        $item->transactions = $item->user->transactions()->paginate(self::MAX_TRANSACTIONS);
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->review_rating = $user->review_rating;
        $item->review_good = $user->review_good;
        $item->review_bad = $user->review_bad;
        return $item;
    }

    /**
     *
     * Function  profileData
     * Mazkur metod profile  bo'limini ochib beradi
     * @param $user
     * @return ProfileDataItem
     */
    public function profileData($user): ProfileDataItem
    {
        $item = new ProfileDataItem();
        $item->task = Task::query()->where('user_id', Auth::id())->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->categories = Category::withTranslations(['ru', 'uz'])->get();
        $item->review_bad = $user->review_bad;
        $item->review_good = $user->review_good;
        $item->review_rating = $user->review_rating;
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $user_categories = UserCategory::query()->where('user_id',$user->id)->pluck('category_id')->toArray();
        $item->user_category = Category::query()->whereIn('id',$user_categories)->get();
        return $item;
    }

    /**
     *
     * Function  userReviews
     * Mazkur metod userga qoldirilgan reviewlar chiqarib beradi
     * @param $user
     * @param $performer
     * @param $review
     * @return Builder[]|Collection
     */
    public static function userReviews($user, $performer, $review)
    {
        $reviews = Review::query()->whereHas('task')->where(['user_id' => $user->id]);

        if (isset($performer)) {
            $reviews->where(['as_performer' => $performer]);
        }
        switch ($review){
            case 'good' :
                $reviews->where(['good_bad' => 1]);
                break;
            case 'bad' :
                $reviews->where(['good_bad' => 0]);
                break;
        }
        return $reviews->orderByDesc('created_at')->get();
    }

    /**
     *
     * Function  createPortfolio
     * Mazkur metod portfolio tablega rasmlarni saqlash
     * @param $request
     * @return
     */
    public function createPortfolio($request)
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $request->except('images');
        $data['user_id'] = $user->id;
        if ($request->hasFile('images')) {
            $image = [];
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/storage/portfolio/' . $user->name . '/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        return Portfolio::query()->create($data);
    }

    /**
     *
     * Function  updatePortfolio
     * Mazkur metod portfolio rasmlarni tahrirlash
     * @param $request
     * @param $portfolio
     * @return mixed
     */
    public function updatePortfolio($request, $portfolio)
    {
        $user = $portfolio->user;
        $imgData = $portfolio->image ? json_decode($portfolio->image) : [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/storage/portfolio/' . $user->name . '/', $filename);
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
     * @param $user
     * @param $link
     * @return array
     */
    #[ArrayShape([])]
    public function videoStore($user, $link): array
    {
        switch (true){
            case str_starts_with($link, 'https://youtu.be/') :
                $user->youtube_link =  str_replace('https://youtu.be', 'https://www.youtube.com/embed', $link);
                $user->save();
                $message = trans('trans.Video added successfully.');
                $success = true;
                break;
            case str_starts_with($link, 'https://www.youtube.com/') :
                $user->youtube_link = str_replace('watch?v=', 'embed/', $link);
                $user->save();
                $message = trans('trans.Video added successfully.');
                $success = true;
                break;
            default :
                $message = trans('trans.Link should be from YouTube.');
                $success = false;
                break;
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    /**
     *
     * Function  balance
     * Mazkur metod cash bladega balansni chiqaradi
     * @param $request
     * @return array
     */
    #[ArrayShape([])]
    public function balance($period, $from, $to, $type): array
    {
        /** @var User $user */
        $user = auth()->user();
        /** @var WalletBalance $balance */
        $balance = WalletBalance::query()->where('user_id', $user->id)->first();
        if ($balance !== null)
            $balance = $balance->balance;
        else
            $balance = 0;
        $transactions = Transaction::query()->where(['transactionable_id' => $user->id])->where('state', 2);

        switch ($type){
            case 'in' :
                $transactions = $transactions->whereIn('payment_system', Transaction::METHODS);
                break;
            case 'out' :
                $transactions = $transactions->where('payment_system', '=', 'Task');
                break;
        }
        $now = Carbon::now();
        switch (true){
            case $period :
                $transactions = match ($period) {
                    'month' => $transactions->where('created_at', '>', $now->subMonth()),
                    'week' => $transactions->where('created_at', '>', $now->subWeek()),
                    'year' => $transactions->where('created_at', '>', $now->subYear()),
                };
                break;
            case $from && $to :
                $transactions = $transactions->where('created_at', '>', $from)
                    ->where('created_at', '<', $to);
                break;
        }
        return [
            'balance' => $balance,
            'transaction' => TransactionHistoryCollection::collection($transactions->orderByDesc('created_at')->paginate(self::MAX_TRANSACTIONS))->response()->getData(true)
        ];
    }

    /**
     *
     * Function  phoneUpdate
     * Mazkur metod telefon raqamni tahrirlaydi
     * @param $phoneNumber
     * @return array
     * @throws \Exception
     */
    #[ArrayShape([])]
    public function phoneUpdate($phoneNumber): array
    {

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
            $message = random_int(100000, 999999);
            $phone_number = $user->phone_number;
            $user->verify_code = $message;
            $user->save();
            SmsMobileService::sms_packages(correctPhoneNumber($phone_number), config('app.name').' '. __("Код подтверждения") . ' ' . $message);
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
     * @param $data
     * @return JsonResponse
     */
    public function changePassword($data): JsonResponse
    {
        /** @var User $user */
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
     * @param $filename
     * @param $user
     */
    public function changeAvatar($filename, $user): void
    {
        $destination = 'storage/'. $user->avatar;
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $imageName = "user-avatar/" . $filename->getClientOriginalName();
        $filename->move(public_path() . '/storage/user-avatar/', $imageName);
        $data['avatar'] = $imageName;
        $user->update($data);
    }

    /**
     *
     * Function  updateSettings
     * Mazkur metod settingni tahrirlash
     * @param $request
     */
    public function updateSettings($request): void
    {
        $validated = $request->validated();
        unset($validated['age']);
        /** @var User $user */
        $user = auth()->user();
        if ($validated['email'] !== $user->email) {
            $validated['is_email_verified'] = 0;
            $validated['email_old'] = $user->email;
        }
        $user->update($validated);
        $user->save();
    }

    /**
     *
     * Function  notifications
     * Mazkur method setting notification api
     * @param $request
     * @return array|string
     */
    public function notifications($request): array|string
    {
        $notification = $request->get('notification');
        /** @var User $user */
        $user = auth()->user();
        switch ($notification){
            case 1 :
                $user->news_notification = 1;
                $message = trans('trans.Notifications turned on.');
                break;
            case 0 :
                $user->news_notification = 0;
                $message = trans('trans.Notifications turned off.');
                break;
        }
        $user->save();
        return $message ?? 'Success';
    }

    /**
     *
     * Function  subscribeToCategory
     * Mazkur metod setting categorylarni tahrirlash
     * @param array $categories
     * @param $user
     * @param int $sms_notification
     * @param int $email_notification
     * @return array
     */
    public function subscribeToCategory(array $categories, $user, int $sms_notification, int $email_notification): array
    {
        $user->role_id = User::ROLE_PERFORMER;
        $user->save();
        $user_exists = UserCategory::query()->where('user_id',$user->id)->get();
        if($user_exists){
            UserCategory::query()->where('user_id',$user->id)->delete();
        }
        foreach ($categories as $category) {
            UserCategory::query()->create([
                'user_id'=> $user->id,
                'category_id'=>$category,
            ]);
        }

        if (!$sms_notification)
            $sms_notification = null;

        if (!$email_notification)
            $email_notification = null;

        $user->update(['sms_notification' => $sms_notification, 'email_notification' => $email_notification]);

        return [
            'success' => true,
            'data' => [
                'message' => trans('trans.You successfully subscribed for notifications by task categories.')
            ]
        ];
    }

    public static function walletBalance($user)
    {
        return ($user && $user->walletBalance) ? ($user->walletBalance->balance) : (null);
    }

    public function verifyCategory($user): VerificationCategoryItem
    {
        $item = new VerificationCategoryItem();
        $item->categories = Category::query()->where('parent_id', null)->orderBy("order")->get();
        $item->categories2 = Category::query()->where('parent_id', '<>', null)->select('id', 'parent_id', 'name')->orderBy("order")->get();
        $item->user_categories = UserCategory::query()->where('user_id',$user->id)->pluck('category_id')->toArray();
        return $item;
    }

    public function deleteImage($image, Portfolio $portfolio): bool
    {
        File::delete(public_path() . '/storage/portfolio/' . $image);
        $images = json_decode($portfolio->image);
        $updatedImages = array_diff($images, [$image]);
        $portfolio->image = json_encode(array_values($updatedImages));
        $portfolio->save();
        return true;
    }

    public function portfolioUpdate($data, Portfolio $portfolio): bool
    {
        $images = array_merge(json_decode(session()->has('images') ? session('images') : '[]'), json_decode($portfolio->image));
        session()->forget('images');
        $data['image'] = json_encode($images);
        $portfolio->update($data);
        $portfolio->save();
        return true;
    }
}
