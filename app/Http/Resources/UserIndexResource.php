<?php

namespace App\Http\Resources;

use App\Models\BlockedUser;
use App\Models\Portfolio;
use App\Models\UserCategory;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Category;
use App\Models\WalletBalance;
use Illuminate\Support\Facades\File;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $gender
 * @property mixed $name
 * @property mixed $id
 * @property mixed $avatar
 * @property mixed $last_seen
 * @property mixed $is_phone_number_verified
 * @property mixed $is_email_verified
 */
class UserIndexResource extends JsonResource
{
    protected string $locale;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(isset($this->password)) {
            $socialPassword=false;
        }
        else{
            $socialPassword=true;
        }

        $this->locale = app()->getLocale();
        $file = "storage/portfolio/{$this->name}";
        if (!file_exists($file)) {
            File::makeDirectory($file);
        }
        $b = File::directories(public_path("storage/portfolio/{$this->name}"));
        $directories = array_map('basename', $b);
        if (WalletBalance::query()->where('user_id', $this->id)->first() !== null){
            $balance = WalletBalance::query()->where('user_id', $this->id)->first()->balance;
        }else{
            $balance = 0;
        }

        $achievements = [];

        // check verify part
        if ($this->is_email_verified && $this->is_phone_number_verified) {
            $email_phone_photo = asset('images/verify.png');
            $message = __('Номер телефона и Е-mail пользователя подтверждены');
        }
        else {
            $email_phone_photo = asset('images/verify_gray.png');
            $message = __('Номер телефона и Е-mail пользователя неподтверждены');
        }
        $achievements[] = [
            'image' => $email_phone_photo,
            'message' => $message
        ];

        $service = new ProfileService();
        $item = $service->profileData($this);

        // check top performer part
        if (in_array($this->id, $item->top_users, true)) {
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
        if ($this->reviews >= 50) {
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

        $goodReviews = $this->goodReviews();
        $lastReview = $goodReviews->get()->last();
        if((int)$this->gender === 1){
            $date_gender = __('Был онлайн');
        }else{
            $date_gender = __('Была онлайн');
        }
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($this->last_seen >= $date) {
            $lastSeen = __('В сети');
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            if(app()->getLocale()==='uz'){
                $lastSeen = $seenDate->diffForHumans().' onlayn edi';
            }else{
                $lastSeen = $date_gender. $seenDate->diffForHumans();
            }
        }
        $age = Carbon::parse($this->born_date)->age;
        $born_date = Carbon::parse($this->born_date)->format('Y-m-d');
        $user_exists = BlockedUser::query()->where('user_id',auth()->id())->where('blocked_user_id',$this->id)->exists();
        if(!$user_exists){
            $blocked_user = 0;
            $user_avatar = asset('storage/'.$this->avatar);
        }else{
            $blocked_user = 1;
            $user_avatar = asset("images/block-user.jpg");
        }

        $user_categories = UserCategory::query()->where('user_id',$this->id)->pluck('category_id')->toArray();
        $categories = CategoryIndexResource::collection(Category::query()
            ->select('id', 'parent_id', 'name', 'ico')
            ->whereIn('id', $user_categories)
            ->get());
        $performed_tasks_count = [];
        foreach ($categories as $category) {
            $performed_tasks_count[] = [
                'name' => $category->getTranslatedAttribute('name')
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'social_password'=> $socialPassword,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'avatar' => $user_avatar,
            'video' => $this->youtube_link,
            'active_task' => $this->active_task,
            'active_step' => $this->active_step,
            'tasks_count' => $performed_tasks_count,
            'achievements' => $achievements,
            'phone_number' => correctPhoneNumber($this->phone_number),
            'location' => $this->location,
            'district' => $this->district,
            'age' => $age,
            'description' => $this->description,
            'categories' => $categories,
            'email_verified' => boolval($this->is_email_verified),
            'phone_verified' => boolval($this->is_phone_number_verified),
            'google_id' => $this->google_id,
            'facebook_id' => $this->facebook_id,
            'born_date' => $born_date,
            'created_tasks' => Task::query()->where(['user_id' => $this->id])->whereIn('status', [Task::STATUS_OPEN, Task::STATUS_RESPONSE, Task::STATUS_IN_PROGRESS, Task::STATUS_COMPLETE, Task::STATUS_NOT_COMPLETED, Task::STATUS_CANCELLED])->get()->count(),
            'performed_tasks' => Task::query()->where(['performer_id' => $this->id])->where('status', Task::STATUS_COMPLETE)->get()->count(),
            'reviews' => [
                'review_bad' => $this->review_bad,
                'review_good' => $this->review_good,
                'rating' => $this->review_rating,
                'last_review' => $lastReview ? [
                    'description' => $lastReview->description,
                    'reviewer_name' => $lastReview->reviewer_name
                ] : null
            ],
            'phone_number_old' => $this->phone_number_old,
            'system_notification' =>$this->system_notification,
            'news_notification' => $this->news_notification,
            'portfolios' => PortfolioIndexResource::collection($this->portfolios),
            'portfolios_count' =>Portfolio::query()->where('user_id',$this->id)->get()->count(),
            'views' => $this->performer_views()->count(),
            'directories' => $directories,
            'wallet_balance' => $balance,
            'work_experience'=>$this->work_experience,
            'last_seen' => $lastSeen,
            'gender'=> $this->gender,
            'blocked_user'=> $blocked_user,
            'created_at' => $this->created_at
        ];
    }
}
