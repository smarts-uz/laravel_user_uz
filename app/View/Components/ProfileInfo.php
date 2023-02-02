<?php

namespace App\View\Components;

use App\Models\BlogNew;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class ProfileInfo extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $news;
    public $user;

    public function __construct()
    {
        $this->news = BlogNew::query()->latest()->take(3)->get();
        $this->user = Auth::user();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.profile-info');
    }
}
