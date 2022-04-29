<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Review;
use App\Models\Task;
use App\Models\User;
use App\Models\WalletBalance;
use App\Services\Profile\ProfileService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;

class UserIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $file = "Portfolio/{$this->name}";
        if (!file_exists($file)) {
            File::makeDirectory($file);
        }
        $b = File::directories(public_path("Portfolio/{$this->name}"));
        $directories = array_map('basename', $b);
        if (WalletBalance::query()->where('user_id', $this->id)->first() != null)
            $balance = WalletBalance::query()->where('user_id', $this->id)->first()->balance;
        else
            $balance = 0;

        $achievements = [];
        if ($this->is_email_verified && $this->is_phone_number_verified) {
            $email_phone_photo = asset('images/verify.png');
            $message = __('Номер телефона и Е-mail пользователя подтверждены');
            $achievements[] = [
                'image' => $email_phone_photo,
                'message' => $message
            ];
        }
        else {
            $email_phone_photo = asset('images/verify_gray.png');
            $message = __('Номер телефона и Е-mail пользователя неподтверждены');
            $achievements[] = [
                'image' => $email_phone_photo,
                'message' => $message
            ];
        }
        $service = new ProfileService();
        $item = $service->profileData($this);
        $about = $item->about;
        $task_count = $item->task_count;
        if ($this->role_id == 2) {
            foreach ($about as $rating) {
                if ($rating->id == $this->id) {
                    $best = asset('images/best.png');
                    $message = __('Входит в ТОП-20 исполнителей User.uz');
                    $achievements[] = [
                        'image' => $best,
                        'message' => $message
                    ];
                }
            }
            if ($task_count >= 50) {
                $task_count = asset('images/50.png');
                $message = __('Более 50 выполненных заданий');
                $achievements[] = [
                    'image' => $task_count,
                    'message' => $message
                ];
            } else {
                $task_count = asset('images/50_gray.png');
                $message = __('Более 50 выполненных заданий');
                $achievements[] = [
                    'image' => $task_count,
                    'message' => $message
                ];
            }
        } else {
            $best = asset('images/best_gray.png');
            $message = __('Не входит в ТОП-20 всех исполнителей User.uz');
            $achievements[] = [
                'image' => $best,
                'message' => $message
            ];
            $task_count = asset('images/50_gray.png');
            $message = __('Более 50 выполненных заданий');
            $achievements[] = [
                'image' => $task_count,
                'message' => $message
            ];
        }
        $tasks = Task::query()->where(['performer_id' => $this->id])->get();
        $performed_tasks = $tasks->groupBy('category_id');
        $performed_tasks_count = [];
        foreach ($performed_tasks as $id => $task) {
            $performed_tasks_count[] = [
                'name' => Category::query()->find($id)->name,
                'count' => __('Выполнено ').$task->count().__(' заданий')
            ];
        }
        $lastReview = Review::query()->where(['user_id' => $this->id, 'good_bad' => 1])->get()->last();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'avatar' => asset($this->avatar),
            'tasks_count' => $performed_tasks_count,
            'achievements' => $achievements,
            'phone_number' => $this->phone_number,
            'location' => $this->location,
            'district' => $this->district,
            'age' => $this->age,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'email_verified' => boolval($this->is_email_verified),
            'phone_verified' => boolval($this->is_phone_number_verified),
            'google_id' => $this->google_id,
            'facebook_id' => $this->facebook_id,
            'born_date' => $this->born_date,
            'created_tasks' => Task::query()->where(['user_id' => $this->id])->get()->count(),
            'performed_tasks' => Task::query()->where(['performer_id' => $this->id])->get()->count(),
            'reviews' => [
                'review_bad' => $this->review_bad,
                'review_good' => $this->review_good,
                'rating' => $this->review_rating,
                'last_review' => [
                    'description' => $lastReview->description,
                    'reviewer_name' => User::query()->find($lastReview->reviewer_id)->name
                ]
            ],
            'phone_number_old' => $this->phone_number_old,
            'system_notification' =>$this->system_notification,
            'news_notification' => $this->news_notification,
            'portfolios' => PortfolioResource::collection($this->portfolios),
            'views' => $this->views,
            'directories' => $directories,
            'wallet_balance' => $balance,
            'last_seen' => $this->last_seen
        ];
    }
}
