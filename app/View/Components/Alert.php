<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;
    public $icon;
    public $title;

    public function __construct($type = 'info', $message = 'This is an alert message.', $icon = null, $title = null)
    {
        $this->type = $type;
        $this->message = $message;
        $this->icon = $icon;
        $this->title = $title ?: ucfirst($type) . ' Alert';
    }

    public function render()
    {
        return view('components.alert');
    }
}