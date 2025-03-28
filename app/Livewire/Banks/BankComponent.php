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

    protected $rules = [
        'bank.name' => 'required|string|max:255',
    ];

    public function mount()
    {
        $this->resetBank();
    }

    public function updatingSearch()
    {
        $this->resetPage(); // Reset pagination when searching
    }

    public function addNew()
    {
        $this->resetBank();
    }

    public function editItem($id)
    {
        $this->bank = Bank::findOrFail($id);
    }

    public function deleteItem($id)
    {
        Bank::destroy($id);

        $this->dispatchBrowserEvent('success-notification', [
            'message' => 'Bank deleted successfully.'
        ]);
    }

    public function addEntry()
    {
        dd($this->bank);
        $this->bank = $this->bank ?? new Bank();
        $this->validate();
        $this->bank->save();
        $this->resetBank();

        $this->dispatchBrowserEvent('close-modal'); // Fix modal close issue
        $this->dispatchBrowserEvent('success-notification', [
            'message' => 'Bank added successfully.'
        ]);
    }

    private function resetBank()
    {
        $this->bank = new Bank(); // Reset model
    }

    public function render()
    {
        $banks = Bank::where('name', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.banks.bank-component', compact('banks'));
    }
}
