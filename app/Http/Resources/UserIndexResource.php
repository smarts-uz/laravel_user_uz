<?php

namespace App\Http\Resources;

use App\Models\WalletBalance;
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'settings' => json_decode($this->settings),
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
            'reviews' => $this->reviews,
            'phone_number_old' => $this->phone_number_old,
            'system_notification' =>$this->system_notification,
            'news_notification' => $this->news_notification,
            'portfolios' => PortfolioResource::collection($this->portfolios),
            'views' => $this->views,
            'directories' => $directories,
            'wallet_balance' => $balance
        ];
    }
}
