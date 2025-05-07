<?php

namespace App\Livewire\Admin\DayBook;

use App\Models\DailyBookDetail;
use Livewire\Component;

class DayBookComponent extends Component
{
    public $dailyBooks = [];
    public $opening_balance;
    public $closing_balance;
    public $dailyBookDate;
    public $all_opening_balance;
    public $all_closing_balance;
    public $fromDate;
    public $toDate;
    public $total_credit = 0;
    public $total_debit = 0;
    public $net_balance = 0;

    public function getOpeningBalance($date)
    {
        $record = DailyBookDetail::whereDate('date', $date)
            ->orderBy('id', 'asc')
            ->first();

        return $record ? $record->opening_balance : 0;
    }
    public function getClosingBalance($date)
    {
        $record = DailyBookDetail::whereDate('date', $date)
            ->orderBy('id', 'desc')
            ->first();

        return $record ? $record->closing_balance : 0;
    }
    public function mount()
    {
        $this->loadBookDetails();
    }
    public function loadBookDetails()
    {
        $this->dailyBooks = DailyBookDetail::selectRaw('MAX(id) as id,date, SUM(debit) as debit, SUM(credit) as credit')
            ->when(!empty($this->dailyBookDate), function ($query) {
                $query->whereDate('date', $this->dailyBookDate);
            })
            ->groupBy('date')
            ->orderByDesc('id')
            ->get();
    }
    public function render()
    {


        return view('livewire.admin.day-book.day-book-component');
    }
}
