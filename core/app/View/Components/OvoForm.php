<?php

namespace App\View\Components;

use App\Models\Form;
use Illuminate\View\Component;

class OvoForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $identifier;
    public $identifierValue;
    public $form;
    public $formData;
    public $filledData;
    public $noFileType;
    public function __construct($identifier, $identifierValue, $filledData = [], $noFileType = false)
    {
        $this->identifier = $identifier;
        $this->identifierValue = $identifierValue;
        $this->form = Form::where($this->identifier, $this->identifierValue)->first();
        $this->formData = @$this->form->form_data ?? [];
        $this->filledData = $filledData;
        $this->noFileType = $noFileType;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ovo-form');
    }
}
