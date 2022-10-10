<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\Category;
use App\Models\WalletBalance;
use Illuminate\Support\Facades\File;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $file = "portfolio/{$this->name}";
        if (!file_exists($file)) {
            File::makeDirectory($file);
        }
        $b = File::directories(public_path("portfolio/{$this->name}"));
        $directories = array_map('basename', $b);
        if (WalletBalance::query()->where('user_id', $this->id)->first() != null)
            $balance = WalletBalance::query()->where('user_id', $this->id)->first()->balance;
        else
            $balance = 0;

        $achievements = [];

        // check verify part
        if ($this->is_phone_number_verified) {
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
        $task_count = $item->task_count;

        // check top performer part
        if (in_array($this->id, $item->top_users)) {
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
        if ($task_count >= 50) {
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

        $tasks = Task::query()->where(['performer_id' => $this->id])->where('status', Task::STATUS_COMPLETE)->latest()->get();
        $performed_tasks = $tasks->groupBy('category_id');
        $performed_tasks_count = [];
        foreach ($performed_tasks as $id => $task) {
            if (Category::query()->find($id) !== null) {
                $performed_tasks_count[] = [
                    'name' => Category::query()->find($id)->getTranslatedAttribute('name'),
                    'count' => __('Выполнено ') . ' ' . $task->count() . __(' заданий')
                ];
            }
        }
        $goodReviews = $this->goodReviews();
        $lastReview = $goodReviews->get()->last();
        $date = Carbon::now()->subMinutes(2)->toDateTimeString();
        if ($this->last_seen >= $date) {
            $lastSeen = 'online';
        } else {
            $seenDate = Carbon::parse($this->last_seen);
            $seenDate->locale(app()->getLocale() . '-' . app()->getLocale());
            $lastSeen = $seenDate->diffForHumans();
        }
        $age = Carbon::parse($this->born_date)->age;
        $born_date = Carbon::parse($this->born_date)->format('Y-m-d');
        return [
            'id' => $this->id,
            'name' => $this->name,
            'social_password'=> $socialPassword,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'avatar' => asset('storage/'.$this->avatar),
            'video' => $this->youtube_link,
            'tasks_count' => $performed_tasks_count,
            'achievements' => $achievements,
            'phone_number' => $this->phone_number,
            'location' => $this->location,
            'district' => $this->district,
            'age' => $age,
            'description' => $this->description,
            'categories' => CategoryIndexResource::collection(Category::query()
                ->select('id', 'parent_id', 'name', 'ico')
                ->whereIn('id', explode(',', $this->category_id))
                ->get()),
            'email_verified' => boolval($this->is_email_verified),
            'phone_verified' => boolval($this->is_phone_number_verified),
            'google_id' => $this->google_id,
            'facebook_id' => $this->facebook_id,
            'born_date' => $born_date,
            'created_tasks' => Task::query()->where(['user_id' => $this->id])->whereIn('status', [1, 2, 3, 4, 5, 6])->get()->count(),
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
            'views' => $this->performer_views()->count(),
            'directories' => $directories,
            'wallet_balance' => $balance,
            'last_seen' => $lastSeen,
            'created_at' => $this->created_at
        ];
    }
}
