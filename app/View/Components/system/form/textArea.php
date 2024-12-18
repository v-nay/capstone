<?php

namespace App\View\Components\system\form;

use Illuminate\View\Component;

class textArea extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.system.form.text-area');
    }
}
