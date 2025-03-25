<?php

namespace App\Livewire\Banks;

use Livewire\Component;

class BankComponent extends Component
{
    public $pageTitle;

    public function mount()
    {
        $this->pageTitle = 'All Banks';
    }
    public function render()
    {
        return view('livewire.banks.bank-component');
    }
}
