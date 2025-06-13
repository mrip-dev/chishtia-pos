<?php

namespace App\Livewire;

use Livewire\Component;

class Select2 extends Component
{
    public $selectid = null;
    public $options = [];
    public $name;
    public $placeholder = 'Select an option';

    public function mount( $id = null, $options = [],$name, $placeholder = 'Select an option')
    {
        $this->name = $name;
        $this->options = $options;
        $this->selectid = $id;
        $this->placeholder = $placeholder;
    }

    public function render()
    {
        return view('livewire.select2');
    }
}
