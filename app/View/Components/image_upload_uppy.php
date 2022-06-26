<?php

namespace App\View\Components;

use Illuminate\View\Component;

class image_upload_uppy extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $route;
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image_upload_uppy');
    }
}
