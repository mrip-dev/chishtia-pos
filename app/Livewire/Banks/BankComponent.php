<?php

namespace App\Livewire\Banks;

use App\Models\Bank;
use Livewire\Component;
use Livewire\WithPagination;

class BankComponent extends Component
{
    use WithPagination;
    public $bank;
    public $search = '';
    public $perPage = 10;
    public $name;

    protected $rules = [
        'bank.name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->bank = new Bank(); // Initialize the model for units
    }
    public function addNew()
    {
        $this->bank = new Bank(); // Reset the bank model
        $this->resetInputFields();
    }
    public function editItem($id)
    {
        $this->bank = Bank::find($id);
    }
    public function deleteItem($id)
    {
        Bank::find($id)->delete();
        $this->dispatchBrowserEvent('success-notification', [
            'message' => 'Bank deleted successfully.'
        ]);
    }
    public function addEntry()
    {
        $this->bank = $this->bank ?? new Bank(); // Ensure bank is an instance
        $this->validate();
        $this->bank->save();
        $this->resetInputFields();
        $this->dispatchBrowserEvent('close-modal', ['id' => 'bankComponentModal']);
        $this->dispatchBrowserEvent('success-notification', [
            'message' => 'Bank added successfully.'
        ]);
    }
    private function resetInputFields()
    {
        $this->name = ''; // Reset unit name

    }
    public function render()
    {
        $banks = Bank::where('name', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);
        return view('livewire.banks.bank-component', compact('banks'));
    }
}
