<?php

namespace App\Services\Profile;

use App\Utils\PaginateCollection;
use Exception;
use JsonException;
use App\Item\{ProfileCashItem, ProfileDataItem, ProfileSettingItem, VerificationCategoryItem};
use App\Models\{BlockedUser, Region,
    ResponseTemplate, Review, Session,
    Task, Transaction, User, UserCategory,
    WalletBalance, Portfolio, Category};
use App\Services\CustomService;
use App\Services\SmsMobileService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\{Facades\Auth, Facades\Cache, Facades\Hash, Facades\File};
use JetBrains\PhpStorm\ArrayShape;
use League\Flysystem\WhitespacePathNormalizer;
use RealRashid\SweetAlert\Facades\Alert;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;


class ProfileService
{

    public static function log($data): void
    {
        if (PHP_SAPI === 'cli') {
            var_dump($data);
        }
    }

    /**
     * user ma'lumotlarini qaytaradi api uchun
     * @param $user_id
     * @return JsonResponse
     */
    #[ArrayShape(['data' => "array"])]
    public function index($user_id): JsonResponse
    {
        $user = User::find($user_id);
        if (isset($user->password)) {
            $socialPassword = false;
        } else {
            $socialPassword = true;
        }

        $norms = new WhitespacePathNormalizer;

                $dirStorage = public_path('storage');
        $dirStorage = $norms->normalizePath($dirStorage);
        If (PHP_OS === 'Linux')
            $dirStorage = "/{$dirStorage}";

        $suffixAvatarMale = 'users/default_male.png';
        $suffixAvatarFeMale = 'users/default_female.png';


        self::log('$dirStorage: ' .$dirStorage );

        $user->locale = app()->getLocale();
        $fileAvatar = $dirStorage.'/'.$user->avatar;
        $fileAvatarMale = $dirStorage.'/'.$suffixAvatarMale;
        $fileAvatarFeMale = $dirStorage.'/'.$suffixAvatarFeMale;

        self::log('$fileAvatar: '.$fileAvatar );
        self::log('$fileAvatarMale: '.$fileAvatarMale );
        self::log('$fileAvatarFeMale: '.$fileAvatarFeMale );


        $wallet = WalletBalance::query()->where('user_id', $user->id)->first();

        if ($wallet !== null) {
            $balance = $wallet->balance;
        } else {
            $balance = 0;
        }

        $achievements = [];

        // check verify part
        if ($user->is_email_verified && $user->is_phone_number_verified) {
            $email_phone_photo = asset('images/verify.png');
            $message = __('Номер телефона и Е-mail пользователя подтверждены');
        } else {
            $email_phone_photo = asset('images/verify_gray.png');
            $message = __('Номер телефона и Е-mail пользователя неподтверждены');
        }
        $achievements[] = [
            'image' => $email_phone_photo,
            'message' => $message
        ];

        $service = new self();
        $item = $service->profileData($user);

        // check top performer part
        if (in_array($user->id, $item->top_users, true)) {
            $best = asset('images/best.png');
            $message = __('Входит в ТОП-20 исполнителей USer.Uz');
        } else {
            $best = asset('images/best_gray.png');
            $message = __('Не входит в ТОП-20 всех исполнителей USer.Uz');
        }
        $achievements[] = [
            'image' => $best,
            'message' => $message
        ];

        // check completed tasks count bigger than 50
        if ($user->reviews >= 50) {
            $task_count = asset('images/50.png');
            $message = __('Более 50 выполненных заданий');
        } else {
            $task_count = asset('images/50_gray.png');
            $message = __('Не более 50 выполненных заданий');
        }
        $achievements[] = [
            'image' => $task_count,
            'message' => $message
        ];

        $goodReviews = $user->goodReviews();
        $lastReview = $goodReviews->get()->last();
        $lastSeen = (new CustomService)->lastSeen($user);
        $age = Carbon::parse($user->born_date)->age;
        $born_date = Carbon::parse($user->born_date)->format('Y-m-d');
        $user_exists = BlockedUser::query()->where('user_id', $user->id)->where('blocked_user_id', $user->id)->exists();
        if ((int)$user->gender === 1) {
            $suffixAvatar =$suffixAvatarMale;
        } else {
            $suffixAvatar = $suffixAvatarFeMale;
        }
        $suffixAvatar = $norms->normalizePath($suffixAvatar);
        if (!$user_exists) {
            $blocked_user = 0;

            self::log('$suffixAvatar: '.$suffixAvatar);

            if (file_exists($fileAvatar))
            {
                self::log('$fileAvatar is Exists');
                $user_avatar = asset('storage/' . $user->avatar);
            } else {

                self::log('$fileAvatar does Not Exists');
                $user_avatar = $suffixAvatar;
            }

            self::log( $user_avatar);

        } else {
            $blocked_user = 1;
            $user_avatar = asset("images/block-user.jpg");
        }

        $user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        $categories = Category::query()->whereIn('id', $user_categories)->get();
        $data = [];
        foreach ($categories as $category) {
            $data[] = $this->categories($category);
        }
        $user_category = UserCategory::query()->where('user_id', $user->id)->get()->groupBy(static function ($data){
            return (!empty($data->category->parent)) ? $data->category->parent->getTranslatedAttribute('name') : '';
        });
        $performed_tasks_count = [];
        foreach ($user_category as $category_name => $category) {
            $performed_tasks_count[] = [
                'name' => $category_name,
                'childs'=> $this->userCat($category)
            ];
        }

        if($user->is_phone_number_verified){
            $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
        }else{
            $phone_number = '';
        }

        $portfolioData = [];
        foreach ($user->portfolios as $portfolio) {
            $portfolioData[] = $this->portfolioIndex($portfolio);
        }

        $statuses = [
            Task::STATUS_OPEN,
            Task::STATUS_RESPONSE,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_COMPLETE,
            Task::STATUS_NOT_COMPLETED,
            Task::STATUS_CANCELLED
        ];

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'social_password' => $socialPassword,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'avatar' => $user_avatar,
            'video' => $user->youtube_link,
            'active_task' => $user->active_task,
            'active_step' => $user->active_step,
            'tasks_count' => $performed_tasks_count,
            'achievements' => $achievements,
            'phone_number' => $phone_number,
            'location' => $user->location,
            'district' => $user->district,
            'age' => $age,
            'description' => $user->description,
            'categories' => $data,
            'email_verified' => boolval($user->is_email_verified),
            'phone_verified' => boolval($user->is_phone_number_verified),
            'google_id' => $user->google_id,
            'facebook_id' => $user->facebook_id,
            'born_date' => $born_date,
            'created_tasks' => Task::where(['user_id' => $user->id])->whereIn('status', $statuses)->get()->count(),
            'performed_tasks' => Task::where(['performer_id' => $user->id])->where('status', Task::STATUS_COMPLETE)->get()->count(),
            'reviews' => [
                'review_bad' => $user->review_bad,
                'review_good' => $user->review_good,
                'rating' => $user->review_rating,
                'last_review' => $lastReview ? [
                    'description' => $lastReview->description,
                    'reviewer_name' => $lastReview->reviewer_name
                ] : null
            ],
            'phone_number_old' => $user->phone_number_old,
            'system_notification' => $user->system_notification,
            'news_notification' => $user->news_notification,
            'portfolios' => $portfolioData,
            'portfolios_count' => Portfolio::where('user_id', $user->id)->get()->count(),
            'views' => $user->performer_views()->count(),
            'wallet_balance' => $balance,
            'work_experience' => $user->work_experience,
            'last_seen' => $lastSeen,
            'last_version' => setting('admin.last_version',''),
            'last_version_ios' => setting('admin.last_version_ios',''),
            'last_version_android' => setting('admin.last_version_android',''),
            'gender' => $user->gender,
            'role_id' => $user->role_id,
            'blocked_user' => $blocked_user,
            'notification_to' => $user->notification_to,
            'notification_from' => $user->notification_from,
            'notification_off' => $user->notification_off,
            'created_at' => $user->created_at
        ];
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function userCat($category): array
    {
        $data = [];
        foreach ($category as $item) {
            $data[] = [
                'name' => $item->category->getTranslatedAttribute('name'),
                'task_count' => $item->category->tasks()->where('performer_id',$item->user_id)->where('status',Task::STATUS_COMPLETE)->count(),
            ];
        }
        return $data;
    }

    /**
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
     * Function  settingsEdit
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni chiqarib beradi
     * @param $user
     * @param string|null $lang
     * @return ProfileSettingItem
     * @throws FileNotFoundException
     */
    public function settingsEdit($user, ?string $lang = 'uz'): ProfileSettingItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });
        $regions = Cache::remember('regions_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Region::withTranslations($lang)->orderBy("id")->get();
        });

        $item = new ProfileSettingItem();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        $item->regions = collect($regions)->all();
        $item->top_users = User::query()
            ->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')
            ->toArray();
        $item->sessions = Session::query()->where('user_id', $user->id)->get();
        $item->parser = Parser::create();
        $item->user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        $item->tasks = Task::query()->where('user_id', $user->id)->whereIn('status', [Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS,])->count();
        return $item;
    }

    /**
     * Function  settingsUpdate
     * Mazkur metod sozlamalar bo'limida ma'lumotlarni tahrirlaydi
     * @param $data
     * @param $user
     * @return mixed
     */
    public function settingsUpdate($data, $user): mixed
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
     * Function  storeProfilePhoto
     * Mazkur metod user profilidagi rasmni tahrirlaydi
     * @param $files
     * @param $hasFile
     * @param $user
     * @return string|null
     */
    public function storeProfilePhoto($files, $hasFile, $user): ?string
    {
        if ($hasFile) {
            $filename = 'user-avatar/' . $files->getClientOriginalName() . '_' . time() . ".jpg";
            $files->move(public_path() . '/storage/user-avatar/', $filename);
            $user->avatar = $filename;
            $user->save();
            return $filename;
        }
        return null;
    }

    /**
     * Function  profileCash
     * Mazkur metod profile cash bo'limini ochib beradi
     * @param $user
     * @return ProfileCashItem
     */
    public function profileCash($user): ProfileCashItem
    {
        $item = new ProfileCashItem();
        $item->balance = $user->walletBalance;
        $item->transactions = $user->transactions()->paginate(15);
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        return $item;
    }

    /**
     * Function  profileData
     * Mazkur metod profile  bo'limini ochib beradi
     * @param $user
     * @return ProfileDataItem
     */
    public function profileData($user): ProfileDataItem
    {
        $item = new ProfileDataItem();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $performer_category = UserCategory::query()->where('user_id', auth()->id())->get()->groupBy(static function ($data){
            return $data->category->parent->id;
        });
        $item->user_category = [];
        foreach ($performer_category as $category_id => $category) {
            $item->user_category[] = [
                'parent' => Category::query()->where('id',$category_id)->get(),
                'category' => $category
            ];
        }
        return $item;
    }

    /**
     * Function  userReviews
     * Mazkur metod userga qoldirilgan reviewlar chiqarib beradi
     * @param $userId
     * @param $performer
     * @param $review
     * @return array
     */
    public static function userReviews($userId, $performer, $review): array
    {
        $reviews = Review::query()->whereHas('task')->where(['user_id' => $userId]);

        if (isset($performer)) {
            $reviews->where(['as_performer' => $performer]);
        }
        switch ($review) {
            case 'good' :
                $reviews->where(['good_bad' => 1]);
                break;
            case 'bad' :
                $reviews->where(['good_bad' => 0]);
                break;
        }
        $data = [];
        foreach ($reviews->orderByDesc('created_at')->get() as $user_review){
            $user = $user_review->reviewer;
            $task = $user_review->task;
            $lastSeen = (new CustomService)->lastSeen($user);
            $data[] = [
                'id' => $user_review->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_seen' => $lastSeen,
                    'review_good' => $user->review_good,
                    'review_bad' => $user->review_bad,
                    'rating' => $user->review_rating,
                    'avatar' => url('/storage') . '/' . $user->avatar,
                ],
                'description' => $user_review->description,
                'good_bad' => $user_review->good_bad,
                'task' => [
                    'name' => $task->name,
                    'description' => $task->description
                ],
                'created_at' => $user_review->created_at
            ];
        }
        return $data;
    }

    /**
     * Function  createPortfolio
     * Mazkur metod portfolio tablega rasmlarni saqlash
     * @param $user
     * @param $data
     * @param $hasFile
     * @param $files
     * @return array
     */
    public function createPortfolio($user, $data, $hasFile, $files): array
    {
        $data['user_id'] = $user->id;
        if ($hasFile) {
            $image = [];
            foreach ($files as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/storage/portfolio/' . $user->name . '/', $filename);
                $image[] = $filename;
            }
            $data['image'] = json_encode($image);
        }
        $portfolio = Portfolio::query()->create($data);
        return $this->portfolioIndex($portfolio);
    }

    /**
     * Function  updatePortfolio
     * Mazkur metod portfolio rasmlarni tahrirlash
     * @param $hasFile
     * @param $files
     * @param $portfolioId
     * @param $description
     * @param $comment
     * @return array
     * @throws JsonException
     */
    public function updatePortfolio($hasFile, $files, $portfolioId, $description, $comment): array
    {
        $portfolio = Portfolio::find($portfolioId);
        $user = $portfolio->user;
        $imgData = $portfolio->image ? json_decode($portfolio->image) : [];
        if ($hasFile) {
            foreach ($files as $uploadedImage) {
                $filename = $user->name . '/' . time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path() . '/storage/portfolio/' . $user->name . '/', $filename);
                $imgData[] = $filename;
            }
        }
        $data['comment'] = $comment;
        $data['description'] = $description;
        $data['image'] = json_encode($imgData, JSON_THROW_ON_ERROR);
        $portfolio->update($data);
        $portfolio->save();

        return $this->portfolioIndex($portfolio);
    }

    /**
     * Function  videoStore
     * Mazkur metod profilga video saqlash
     * @param $user
     * @param $link
     * @return array
     * https://www.youtube.com/watch?v=nseUTjLfSz4&ab_channel=TeacherAzam
     * https://youtu.be/nseUTjLfSz4
     * https://www.youtube.com/embed/nseUTjLfSz4
     */
    #[ArrayShape([])]
    public function videoStore($user, $link): array
    {
        switch (true) {
            case str_starts_with($link, 'https://youtu.be/') :
                $user->youtube_link = str_replace('https://youtu.be', 'https://www.youtube.com/embed', $link);
                $user->save();
                $message = trans('trans.Video added successfully.');
                $success = true;
                break;
            case str_starts_with($link, 'https://www.youtube.com/') :
                $youtube_link = str_replace('https://www.youtube.com/watch?v=', '', $link);
                $user->youtube_link = 'https://www.youtube.com/embed/'. substr($youtube_link,0,11);
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
            'message' => $message,
            'data'=> $link
        ];
    }

    /**
     * Function  balance
     * Mazkur metod cash bladega balansni chiqaradi
     * @param $period
     * @param $from
     * @param $to
     * @param $type
     * @param $userId
     * @return array
     */
    #[ArrayShape([])]
    public function balance($period, $from, $to, $type, $userId): array
    {
        /** @var WalletBalance $balance */
        $balance = WalletBalance::query()->where('user_id', $userId)->first();
        if ($balance !== null)
            $balance = $balance->balance;
        else
            $balance = 0;
        $transactions = Transaction::query()->where(['transactionable_id' => $userId])->where('state', 2);

        switch ($type) {
            case 'in' :
                $transactions = $transactions->whereIn('payment_system', Transaction::METHODS);
                break;
            case 'out' :
                $transactions = $transactions->where('payment_system', '=', 'Task');
                break;
        }
        $now = Carbon::now();
        switch (true) {
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
        $data = [];
        foreach ($transactions->orderByDesc('created_at')->get() as $item) {
            $data[] = [
                "id" => $item->id,
                "user_id" => $item->transactionable_id,
                "method" => ucfirst($item->payment_system) == 'Task' ? __('Оплата за отклик') : ucfirst($item->payment_system),
                "amount" => ucfirst($item->payment_system) == 'Paynet' ? $item->amount / 100 : $item->amount,
                "status" => strtolower($item->payment_system) == 'task' ? 0 : 1,
                "created_at" => $item->created_at,
                "updated_at" => $item->updated_at,
                "state" => $item->state
            ];
        }
        return [
            'balance' => $balance,
            'transaction' => PaginateCollection::paginate($data,10)
        ];
    }

    /**
     * Function  phoneUpdate
     * Mazkur metod telefon raqamni tahrirlaydi
     * @param $phoneNumber
     * @param $user
     * @return array
     * @throws Exception
     */
    #[ArrayShape([])]
    public function phoneUpdate($phoneNumber, $user): array
    {
        /** @var User $userPhone */
        $userPhone = User::query()->where(['phone_number' => $phoneNumber])->first();
        if ($userPhone && ((int)$userPhone->id !== (int)$user->id)) {
            $messages = trans('trans.User with entered phone number already exists.');
            $success = false;
        } else {
            $user->phone_number_old = $user->phone_number;
            $user->phone_number = $phoneNumber;
            $user->is_phone_number_verified = 0;
            $message = random_int(100000, 999999);
            $phone_number = (new CustomService)->correctPhoneNumber($user->phone_number);
            $user->verify_code = $message;
            $user->save();
            SmsMobileService::sms_packages($phone_number, config('app.name') . ' ' . __("Код подтверждения") . ' ' . $message);
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
     * Function  changePassword
     * Mazkur metod passwordni tahrirlash
     * @param $user
     * @param $data
     * @return JsonResponse
     */
    public function changePassword($user, $data): JsonResponse
    {
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
            'data' => $data
        ]);
    }

    /**
     * Function  changeAvatar
     * Mazkur metod profilda rasm tahrirlash
     * @param $filename
     * @param $user
     */
    public function changeAvatar($filename, $user): void
    {
        $destination = 'storage/' . $user->avatar;
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $imageName = "user-avatar/" . $filename->getClientOriginalName();
        $filename->move(public_path() . '/storage/user-avatar/', $imageName);
        $data['avatar'] = $imageName;
        $user->update($data);
    }

    /**
     * Function  updateSettings
     * Mazkur metod settingni tahrirlash
     * @param $validated
     * @param $user
     */
    public function updateSettings($validated, $user): void
    {
        if ($validated['email'] !== $user->email) {
            $validated['is_email_verified'] = 0;
            $validated['email_old'] = $user->email;
        }
        $user->update($validated);
        $user->save();
    }

    /**
     * Function  notifications
     * Mazkur method setting notification api
     * @param $user
     * @param $notification
     * @return array|string
     */
    public function notifications($user, $notification): array|string
    {
        switch ($notification) {
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
     * Function  subscribeToCategory
     * Mazkur metod setting categorylarni tahrirlash
     * @param array $categories
     * @param $user
     * @param int $sms_notification
     * @param int $email_notification
     * @return array
     */
    #[ArrayShape(['success' => "bool", 'data' => "array"])]
    public function subscribeToCategory(array $categories, $user, int $sms_notification, int $email_notification): array
    {
        $user->role_id = User::ROLE_PERFORMER;
        $user->save();
        $user_exists = UserCategory::query()->where('user_id', $user->id)->get();
        if ($user_exists) {
            UserCategory::query()->where('user_id', $user->id)->delete();
        }
        foreach ($categories as $category) {
            UserCategory::query()->create([
                'user_id' => $user->id,
                'category_id' => $category,
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

    /**
     * Ijrochi bo'lish pagega categoriyalarni qaytaradi
     * @param string|null $lang
     * @return VerificationCategoryItem
     */
    public function verifyCategory(?string $lang = 'uz'): VerificationCategoryItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new VerificationCategoryItem();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        return $item;
    }

    /**
     * Profileda passwordni o'zgartirish
     * @param $user
     * @param $data
     * @return RedirectResponse
     */
    public function change_password($user, $data): RedirectResponse
    {
        switch (true) {
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

    /**
     * portfoliodan $image bo'yicha kelgan rasmni o'chiradi
     * @param $image
     * @param $portfolioId
     * @return bool
     * @throws JsonException
     */
    public function deleteImage($image, $portfolioId): bool
    {
        $portfolio = Portfolio::find($portfolioId);
        File::delete(public_path() . '/storage/portfolio/' . $image);
        $images = json_decode($portfolio->image, false, 512, JSON_THROW_ON_ERROR);
        $updatedImages = array_diff($images, [$image]);
        $portfolio->image = json_encode(array_values($updatedImages), JSON_THROW_ON_ERROR);
        $portfolio->save();
        return true;
    }

    /**
     * porffolioga rasm qo'shadi
     * @param $data
     * @param Portfolio $portfolio
     * @return bool
     */
    public function portfolioUpdate($data, Portfolio $portfolio): bool
    {
        $images = array_merge(json_decode(session()->has('images') ? session('images') : '[]'), json_decode($portfolio->image));
        session()->forget('images');
        $data['image'] = json_encode($images);
        $portfolio->update($data);
        $portfolio->save();
        return true;
    }

    /**
     * porfolio auth userga tegishliligini aniqlaydi
     * @param $portfolio
     * @return void
     */
    public function portfolioGuard($portfolio): void
    {
        if ((int)$portfolio->user_id !== (int)auth()->user()->id) {
            abort(403, "No Permission");
        }
    }

    /**
     * $user ga tegishli portfolioni qaytaradi
     * @param $userId
     * @return array
     */
    public function portfolios($userId): array
    {
        $portfolios = Portfolio::query()->where(['user_id' => $userId])->get();
        $data = [];
        foreach ($portfolios as $portfolio) {
            $data[] = $this->portfolioIndex($portfolio);
        }
        return $data;
    }

    public function portfolioIndex($portfolio): array
    {
        return !empty($portfolio) ? [
            'id' => $portfolio->id,
            'user_id' => $portfolio->user_id,
            'comment' => $portfolio->comment,
            'description' => $portfolio->description,
            'images' => $this->makeAssets(json_decode($portfolio->image??"[]")),
        ]: [];
    }

    public function makeAssets($collection): array
    {
        $arr = [];
        foreach ($collection as $item) {
            $arr[] = asset('/storage/portfolio/'.$item);
        }
        return $arr;
    }

    /**
     * tilni o'zgartirish uchun api
     * @param $lang
     * @param $version
     * @return JsonResponse
     */
    public function changeLanguage($lang, $version): JsonResponse
    {
        if (Auth::guard('api')->check()) {
            cache()->forever('lang' . auth()->id(), $lang);
            app()->setLocale($lang);
            /** @var User $user */
            $user = auth()->user();
            $user->version = $version;
            $user->save();
            return response()->json([
                'success' => true,
                'data' => [
                    'message' => trans('trans.Language changed successfully.')
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => trans('trans.Language changed successfully.')
        ]);
    }

    /**
     * profilni o'chirish uchun telefon raqamga kod yuboradi
     * @param $user
     * @return JsonResponse
     * @throws Exception
     */
    public function self_delete($user): JsonResponse
    {
        $tasks = Task::query()->where('user_id', $user->id)->whereIn('status', [Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS,])->count();
        if($tasks>0){
            return response()->json([
                'success' => false,
                'message' => __('У вас есть задачи в процессе, вы не можете удалить свой профиль')
            ]);
        }

        if ($user->phone_number && strlen($user->phone_number) === 13  && $user->is_phone_number_verified) {
            VerificationService::send_verification('phone', $user, $user->phone_number);
            return response()->json([
                'success' => true,
                'phone_number' => (new CustomService)->correctPhoneNumber($user->phone_number),
                'message' => __('СМС-код отправлен!')
            ]);
        }


        return response()->json([
            'success' => false,
            'message' => __('Ваш номер не подтвержден')
        ]);
    }

    /**
     * profilni o'chirish uchun telefon raqamga yuborilgan kodni tasdiqlash
     * @param $user
     * @param $code
     * @return JsonResponse
     */
    public function confirmationSelfDelete($user, $code): JsonResponse
    {
        if ((int)$user->verify_code === (int)$code) {
            if (strtotime($user->verify_expiration) >= strtotime(now())) {
                $user->delete();
                return response()->json([
                    'success' => true,
                    'message' => __('Успешно удалено')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Срок действия номера истек')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('Код ошибки')
        ]);
    }

    /**
     * userni block qilish apisi
     * @param $blocked_user_id
     * @param $user_id
     * @return JsonResponse
     */
    public function blocked_user($blocked_user_id, $user_id): JsonResponse
    {
        $blocked_user = BlockedUser::query()->where('user_id', $user_id)->where('blocked_user_id', $blocked_user_id);
        if ($blocked_user->exists()) {
            $blocked_user->delete();
        } else {
            BlockedUser::query()->updateOrCreate([
                'user_id' => $user_id,
                'blocked_user_id' => $blocked_user_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено'),
            'data' => $blocked_user
        ]);
    }

    /**
     * block userlar listini qaytaradi
     * @param $user_id
     * @return array
     */
    public function blocked_user_list($user_id): array
    {
        $blocked_users = BlockedUser::query()->where('user_id', $user_id)->get();
        $data = [];
        foreach ($blocked_users as $blocked_user) {
            $user = $blocked_user->user;
            $lastSeen = (new CustomService)->lastSeen($user);
            $data[] = [
                'id' => $blocked_user->id,
                'user_id'=> $blocked_user->user_id,
                'blocked_user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_seen' => $lastSeen,
                    'avatar' => asset('storage/' . $user->avatar),
                ],
                'created_at' => $blocked_user->created_at
            ];
        }
        return $data;
    }

    /**
     * shablon otklikni qaytaradi
     * @param $userId
     * @return array[]
     */
    #[ArrayShape(['data' => "array"])]
    public function response_template($userId): array
    {
        $templates = ResponseTemplate::query()->where(['user_id' => $userId])->get();
        $data = [];
        foreach ($templates as $template) {
            $data[] = [
                'id' => $template->id,
                'title' => $template->title,
                'text' => $template->text,
                'created_at' => $template->created_at
            ];
        }
        return $data;
    }

    /**
     * shablon otklikni o'chiradi
     * @param $userId
     * @param $templateId
     * @return JsonResponse
     */
    public function response_template_delete($userId, $templateId): JsonResponse
    {
        if ((int)$userId === (int)$templateId->user_id) {
            $templateId->delete();
            return response()->json([
                'success' => true,
                'message' => 'success',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'unsuccessful',
        ]);
    }

    /**
     * Ushbu metod foydalanuvchi tomonidan tanlangan kategoriyalarni qaytaradi
     * @param $userId
     * @return array
     */
    public function userCategory($userId): array
    {
        $user_categories = UserCategory::query()->where('user_id', $userId)->pluck('category_id')->toArray();
        $categories = Category::query()->whereIn('id', $user_categories)->get();
        $data = [];
        foreach ($categories as $category) {
            $data[] = $this->categories($category);
        }
        return $data;
    }

    /**
     * @param $category
     * @return array
     */
    public function categories($category): array
    {
        return !empty($category) ? [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->getTranslatedAttribute('name'),
            'child_count' => $category->childs()->count(),
            'ico' => asset('storage/' . $category->ico),
        ]: [];
    }


}
