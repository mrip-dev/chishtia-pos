<?php

namespace App\Livewire\Admin\DayBook;

use App\Models\DailyBookDetail;
use Livewire\Component;

class DayBookDetailComponent extends Component
{
    public $bookDetails = [];
    public $dailyBookDate;
    public $opening_balance;
    public $closing_balance;
    public $source;
    public function mount($date)
    {
        $this->dailyBookDate = $date;
        $this->loadBookDetails();
    }
    function loadBookDetails()
    {

        $query = DailyBookDetail::query();
        if (!empty($this->source)) {
            $query->where('source', $this->source);
        }
        $query->whereDate('date', $this->dailyBookDate)->orderBy('id', 'desc');
        $this->bookDetails = $query->get();
        $this->calculateBalances();
    }
    public function updatedSource()
    {
        $this->loadBookDetails();
    }
     public function calculateBalances()
    {
        $details = DailyBookDetail::whereDate('date', $this->dailyBookDate)
            ->orderBy('date', 'desc')
            ->get();

        if ($details->isEmpty()) {
            $this->opening_balance = 0;
            $this->closing_balance = 0;
        } else {
            $this->opening_balance = $details->first()?->opening_balance ?? 0;
            $this->closing_balance = $details->last()?->closing_balance ?? 0;
        }
    }
    public function render()
    {
        return view('livewire.admin.day-book.day-book-detail-component');
    }

    public function redirectDataModel($id, $dataModel)
    {

        switch ($dataModel) {

            case 'Sale':
                return redirect()->to('/admin/manage/sale/?module_id=' . $id.'#module_id_' . $id);
                break;
            case 'Purchase':
                return  redirect()->to('/admin/manage/purchase/?module_id=' . $id.'#module_id_' . $id);
                break;
            case 'Expense':
               return redirect()->to('/admin/manage/expense/?module_id=' . $id.'#module_id_' . $id);
                break;
             case 'Stock':
                return redirect()->to('/admin/services/stock-in/?module_id=' . $id.'#module_id_' . $id);
                break;
        }
    }
}
