<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UnsubscribeFooter extends Component
{
    public $unsubscribeUrl;

    /**
     * Create a new component instance.
     */
    public function __construct($unsubscribeUrl = null)
    {
        $this->unsubscribeUrl = $unsubscribeUrl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.unsubscribe-footer', [
            'unsubscribeUrl' => $this->unsubscribeUrl
        ]);
    }
}
