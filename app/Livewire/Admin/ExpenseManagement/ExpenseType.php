<?php

namespace App\Livewire\Admin\ExpenseManagement;

use Livewire\Component;
use App\Models\ExpenseType as ExpenseTypeModel;
use Livewire\WithPagination;

class ExpenseType extends Component
{
    use WithPagination;

    public $name, $typeId, $modalTitle;
    public $confirmingDelete = false;
    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
    ];
    public function expenseTypeModal()
{
    $this->reset(['name', 'typeId']);
    $this->modalTitle = 'Add New Expense Type';

    $this->dispatch('show-expense-type-modal');
}

    public function resetForm()
    {
        $this->reset(['name', 'typeId', 'modalTitle']);
    }

    public function save()
    {
        $this->validate();

        ExpenseTypeModel::updateOrCreate(
            ['id' => $this->typeId],
            ['name' => $this->name]
        );

        $this->dispatch('close-modal');
        $this->resetForm();
        $this->dispatch('notify', status: 'success', message: 'Expense type saved successfully!');
    }

    public function edit($id)
    {
        $type = ExpenseTypeModel::findOrFail($id);
        $this->typeId = $type->id;
        $this->name = $type->name;
        $this->modalTitle = 'Edit Expense Type';
        $this->dispatch('show-expense-type-modal');
    }
     public function confirmDelete($id)
    {
        $this->typeId = $id;
        $this->dispatch('show-delete-modal');
    }


    public function delete()
    {
        // Find the expense type by ID
        $type = ExpenseTypeModel::withCount('expenses')->findOrFail($this->typeId);

        // Check if the expense type has related expenses
        if ($type->expenses_count > 0) {
            // If related expenses are present, show error message
            $this->dispatch('notify', status: 'error', message: 'This expense type has expenses and cannot be deleted.');
            return;
        }

        // Otherwise, delete the expense type
        $type->delete();

        // Close the modal and notify success
        $this->dispatch('close-modal');
        $this->dispatch('notify', status: 'success', message: 'Expense type deleted successfully!');
    }

     public function render()
    {
        $types = ExpenseTypeModel::withCount('expenses')->latest()->paginate(10);
        return view('livewire.admin.expense-management.expense-type', [
            'types' => $types,
            'emptyMessage' => 'No expense type found',
        ]);
    }
}
