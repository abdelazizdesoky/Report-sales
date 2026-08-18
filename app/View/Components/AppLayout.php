<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * @param bool $wide Let the page use the full width of the screen instead
     *                   of the centred reading column. For data tables.
     */
    public function __construct(
        public bool $wide = false
    ) {}

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
