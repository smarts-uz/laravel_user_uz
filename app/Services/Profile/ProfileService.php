<?php

namespace App\Services\Profile;

use App\Http\Resources\{CategoryIndexResource,
    PortfolioIndexResource,
    ResponseTemplateResource,
    ReviewIndexResource,
    TransactionHistoryCollection,
    UserCategoriesResource};
use App\Item\{ProfileCashItem, ProfileDataItem, ProfileSettingItem, VerificationCategoryItem};
use App\Models\{BlockedUser, Region,
    ResponseTemplate, Review, Session,
    Task, Transaction, User, UserCategory,
    WalletBalance, Portfolio, Category};
use App\Services\CustomService;
use App\Services\SmsMobileService;
use App\Services\VerificationService;
use Carbon\Carbon;
use Illuminate\Http\{JsonResponse, RedirectResponse, Resources\Json\AnonymousResourceCollection};
use Illuminate\Support\{Facades\Auth, Facades\Cache, Facades\Hash, Facades\File};
use JetBrains\PhpStorm\ArrayShape;
use League\Flysystem\WhitespacePathNormalizer;
use RealRashid\SweetAlert\Facades\Alert;
use UAParser\Exception\FileNotFoundException;
use UAParser\Parser;


class ProfileService
{
    public const MAX_TRANSACTIONS = 15;


    public static function log($data) {
        if (PHP_SAPI === 'cli')
            var_dump($data);
    }
    /**
     * @param $user
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
        if ((int)$user->gender === 1) {
            $date_gender = __('Был онлайн');
            $suffixAvatar =$suffixAvatarMale;
        } else {
            $date_gender = __('Была онлайн');
            $suffixAvatar = $suffixAvatarFeMale;
        }
        $suffixAvatar = $norms->normalizePath($suffixAvatar);

        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($user->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($user->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if (app()->getLocale() === 'uz') {
                $lastSeen = $seenDate->diffForHumans() . ' onlayn edi';
            } else {
                $lastSeen = $date_gender . $seenDate->diffForHumans();
            }
        }
        $age = Carbon::parse($user->born_date)->age;
        $born_date = Carbon::parse($user->born_date)->format('Y-m-d');
        $user_exists = BlockedUser::query()->where('user_id', $user->id)->where('blocked_user_id', $user->id)->exists();

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
        $categories = CategoryIndexResource::collection(Category::query()
            ->select('id', 'parent_id', 'name', 'ico')
            ->whereIn('id', $user_categories)
            ->get());
        $user_category = UserCategory::query()->where('user_id', $user->id)->get()->groupBy(static function ($data){
            return (!empty($data->category->parent)) ? $data->category->parent->getTranslatedAttribute('name') : '';
        });
        $performed_tasks_count = [];
        foreach ($user_category as $category_name => $category) {
            $performed_tasks_count[] = [
                'name' => $category_name,
                'childs'=> UserCategoriesResource::collection($category)
            ];
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
            'nam' => $user->name,
            'social_password' => $socialPassword,
            'last_n' => $user->last_name,
            'email' => $user->email,
            'ava' => $user_avatar,
            'video' => $user->youtube_link,
            'active_task' => $user->active_task,
            'active_step' => $user->active_step,
            'tasks_count' => $performed_tasks_count,
            'achievements' => $achievements,
            'phone_number' => (new CustomService)->correctPhoneNumber($user->phone_number),
            'location' => $user->location,
            'district' => $user->district,
            'age' => $age,
            'description' => $user->description,
            'categories' => $categories,
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
            'portfolios' => PortfolioIndexResource::collection($user->portfolios),
            'portfolios_count' => Portfolio::where('user_id', $user->id)->get()->count(),
            'views' => $user->performer_views()->count(),
            'wallet_balance' => $balance,
            'work_experience' => $user->work_experience,
            'last_seen' => $lastSeen,
            'last_version' => setting('admin.last_version'),
            'gender' => $user->gender,
            'blocked_user' => $blocked_user,
            'notification_to' => $user->notification_to,
            'notification_from' => $user->notification_from,
            'notification_off' => $user->notification_off,
            'created_at' => $user->created_at
        ];
        return response()->json(['success' => true, 'data' => $data]);
    }

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
        $item->task = Task::query()->where('user_id', Auth::id())->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->count();
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
     *
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
     *
     * Function  profileCash
     * Mazkur metod profile cash bo'limini ochib beradi
     * @param $user
     * @return ProfileCashItem
     */
    public function profileCash($user): ProfileCashItem
    {
        $item = new ProfileCashItem();
        $item->balance = $user->walletBalance;
        $item->task = Task::query()->where('user_id', Auth::id())->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->count();
        $item->transactions = $user->transactions()->paginate(self::MAX_TRANSACTIONS);
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
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
        $item->task = Task::query()->where('user_id', $user->id)->whereIn('status', [
            Task::STATUS_OPEN,
            Task::STATUS_RESPONSE,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_COMPLETE,
            Task::STATUS_NOT_COMPLETED,
            Task::STATUS_CANCELLED])->count();
        $item->portfolios = $user->portfolios()->where('image', '!=', null)->get();
        $item->top_users = User::query()->where('role_id', User::ROLE_PERFORMER)
            ->where('review_rating', '!=', 0)->orderbyRaw('(review_good - review_bad) DESC')
            ->limit(Review::TOP_USER)->pluck('id')->toArray();
        $item->goodReviews = $user->goodReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $item->badReviews = $user->badReviews()->whereHas('task')->whereHas('user')->latest()->get();
        $user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        $item->user_category = Category::query()->whereIn('id', $user_categories)->get();
        return $item;
    }

    /**
     *
     * Function  userReviews
     * Mazkur metod userga qoldirilgan reviewlar chiqarib beradi
     * @param $user
     * @param $performer
     * @param $review
     * @return AnonymousResourceCollection
     */
    public static function userReviews($user, $performer, $review): AnonymousResourceCollection
    {
        $reviews = Review::query()->whereHas('task')->where(['user_id' => $user->id]);

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
        return ReviewIndexResource::collection($reviews->orderByDesc('created_at')->get());
    }

    /**
     *
     * Function  createPortfolio
     * Mazkur metod portfolio tablega rasmlarni saqlash
     * @param $user
     * @param $data
     * @param $hasFile
     * @param $files
     * @return PortfolioIndexResource
     */
    public function createPortfolio($user, $data, $hasFile, $files): PortfolioIndexResource
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
        return new PortfolioIndexResource($portfolio);
    }

    /**
     *
     * Function  updatePortfolio
     * Mazkur metod portfolio rasmlarni tahrirlash
     * @param $hasFile
     * @param $files
     * @param $portfolio
     * @param $description
     * @param $comment
     * @return PortfolioIndexResource
     * @throws \JsonException
     */
    public function updatePortfolio($hasFile, $files, $portfolio, $description, $comment): PortfolioIndexResource
    {
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

        return new PortfolioIndexResource($portfolio);
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
        switch (true) {
            case str_starts_with($link, 'https://youtu.be/') :
                $user->youtube_link = str_replace('https://youtu.be', 'https://www.youtube.com/embed', $link);
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
     * @param $period
     * @param $from
     * @param $to
     * @param $type
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
     * @param $user
     * @return array
     * @throws \Exception
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
     *
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
     *
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
     *
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
     * @param $user
     * @param string|null $lang
     * @return VerificationCategoryItem
     */
    public function verifyCategory($user, ?string $lang = 'uz'): VerificationCategoryItem
    {
        $category = Cache::remember('category_' . $lang, now()->addMinute(180), function () use ($lang) {
            return Category::withTranslations($lang)->orderBy("order")->get();
        });

        $item = new VerificationCategoryItem();
        $item->categories = collect($category)->where('parent_id', null)->all();
        $item->categories2 = collect($category)->where('parent_id', '!=', null)->all();
        $item->user_categories = UserCategory::query()->where('user_id', $user->id)->pluck('category_id')->toArray();
        return $item;
    }

    /**
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
     * @param $image
     * @param Portfolio $portfolio
     * @return bool
     */
    public function deleteImage($image, Portfolio $portfolio): bool
    {
        File::delete(public_path() . '/storage/portfolio/' . $image);
        $images = json_decode($portfolio->image);
        $updatedImages = array_diff($images, [$image]);
        $portfolio->image = json_encode(array_values($updatedImages));
        $portfolio->save();
        return true;
    }

    /**
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
     * @param $user
     * @return JsonResponse
     */
    public function portfolios($user): JsonResponse
    {
        $portfolio = Portfolio::query()->where(['user_id' => $user])->get();
        return response()->json([
            'success' => true,
            'data' => PortfolioIndexResource::collection($portfolio)
        ]);
    }

    /**
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
     * @param $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function self_delete($user): JsonResponse
    {
        if ($user->phone_number && strlen($user->phone_number) === 13) {
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
     * @param $blocked_user_id
     * @return JsonResponse
     */
    public function blocked_user($blocked_user_id): JsonResponse
    {
        $blocked_user = BlockedUser::query()->where('user_id', auth()->id())->where('blocked_user_id', $blocked_user_id);
        if ($blocked_user->exists()) {
            $blocked_user->delete();
        } else {
            BlockedUser::query()->updateOrCreate([
                'user_id' => \auth()->id(),
                'blocked_user_id' => $blocked_user_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Успешно сохранено')
        ]);
    }

    /**
     * @param $user
     * @return JsonResponse
     */
    public function response_template($user): JsonResponse
    {
        $data = ResponseTemplate::query()->where(['user_id' => $user->id])->get();
        return response()->json([
            'success' => true,
            'data' => ResponseTemplateResource::collection($data)
        ]);
    }

    /**
     * @param $user
     * @param $template
     * @return JsonResponse
     */
    public function response_template_delete($user, $template): JsonResponse
    {
        if ((int)$user->id === (int)$template->user_id) {
            $template->delete();
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

}
