<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceManagerComponent extends Component
{
    public Carbon $weekStartDate;
    public ?Attendance $selectedAttendance = null;

    public function mount()
    {
        $this->weekStartDate = now()->startOfWeek();
    }

    public function goToPreviousWeek()
    {
        $this->weekStartDate->subWeek();
        $this->closeModal(); // Close modal when navigating
    }

    public function goToCurrentWeek()
    {
        $this->weekStartDate = now()->startOfWeek();
        $this->closeModal();
    }

    public function goToNextWeek()
    {
        $this->weekStartDate->addWeek();
        $this->closeModal();
    }

    public function showDetails(int $attendanceId)
    {
        $this->selectedAttendance = Attendance::with('admin')->find($attendanceId);
    }

    public function closeModal()
    {
        $this->selectedAttendance = null;
    }

    public function render()
    {
        // --- Data loading logic is now inside render() ---
        $weekEndDate = $this->weekStartDate->copy()->endOfWeek();

        // 1. Get all staff members
        $staffMembers = Admin::orderBy('name')->get();

        // 2. Get all relevant attendance records for the week
        $attendances = Attendance::whereIn('admin_id', $staffMembers->pluck('id'))
            ->whereBetween('date', [$this->weekStartDate, $weekEndDate])
            ->get()
            ->groupBy('admin_id');

        // 3. Structure the data for the view
        foreach ($staffMembers as $staff) {
            $staffAttendancesForWeek = $attendances->get($staff->id);

            if ($staffAttendancesForWeek) {
                $staff->attendancesByDate = $staffAttendancesForWeek->keyBy(function ($item) {
                    return Carbon::parse($item->date)->format('Y-m-d');
                });
            } else {
                $staff->attendancesByDate = collect(); // Assign an empty collection
            }
        }
        // --- End of data loading logic ---

        $dateRange = CarbonPeriod::create($this->weekStartDate, $weekEndDate);

        return view('livewire.admin.attendance-manager-component', [
            'staffWithAttendance' => $staffMembers, // Pass the processed data to the view
            'dateRange' => $dateRange
        ]);
    }
}