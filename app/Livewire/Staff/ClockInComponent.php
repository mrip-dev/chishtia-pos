<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClockInComponent extends Component
{
    public $staff;
    public ?Attendance $todaysAttendance = null;
    public $currentTime;
    public $message = '';
    public $messageType = 'info';

    public function mount()
    {
        $this->staff = Auth::guard('admin')->user(); // Assuming staff log in via the Admin model
        $this->loadTodaysAttendance();
        $this->currentTime = now()->toDateTimeString();
    }

    public function loadTodaysAttendance()
    {
        $this->todaysAttendance = Attendance::where('admin_id', $this->staff->id)
            ->where('date', today())
            ->first();
    }

    public function clockIn()
    {
        // Prevent clocking in if already clocked in
        if ($this->todaysAttendance && $this->todaysAttendance->check_in) {
            $this->message = 'You have already clocked in today.';
            $this->messageType = 'warning';
            return;
        }

        $now = now();
        $startTime = today()->setTime(9, 5, 0); // Office start time is 9:05 AM for late check

        $status = $now->gt($startTime) ? 'late' : 'present';

        $this->todaysAttendance = Attendance::create([
            'admin_id' => $this->staff->id,
            'date' => today(),
            'check_in' => $now,
            'status' => $status,
        ]);

        $this->message = 'Successfully Clocked In at ' . $now->format('h:i A');
        $this->messageType = 'success';
    }

    public function clockOut()
    {
        // Must be clocked in to clock out
        if (!$this->todaysAttendance || !$this->todaysAttendance->check_in) {
            $this->message = 'You have not clocked in yet.';
            $this->messageType = 'danger';
            return;
        }

        // Prevent multiple clock-outs
        if ($this->todaysAttendance->check_out) {
            $this->message = 'You have already clocked out today.';
            $this->messageType = 'warning';
            return;
        }

        $now = now();
        $this->todaysAttendance->update([
            'check_out' => $now,
        ]);

        $this->message = 'Successfully Clocked Out at ' . $now->format('h:i A');
        $this->messageType = 'success';
    }

    public function render()
    {
        // This makes the clock on screen tick in real-time
        $this->currentTime = now()->toDateTimeString();

        return view('livewire.staff.clock-in-component'); // Use your main app layout, or a kiosk-specific one
    }
}