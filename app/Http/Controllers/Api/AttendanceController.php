<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Admin;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Handles clock-in or clock-out based on the provided fingerprint ID.
     */
    public function clockAction(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string',
        ]);

        $employee = Admin::where('fingerprint_id', $request->fingerprint_id)->first();

        if (!$employee) {
            return response()->json(['message' => 'Fingerprint ID not recognized.'], 404);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
                                ->whereDate('date', $today)
                                ->first();

        if (!$attendance) {
            // No record for today, so this is a clock-in
            Attendance::create([
                'employee_id' => $employee->id,
                'date' => $today,
                'clock_in' => Carbon::now(),
                'status' => 'Present', // Or 'Pending' until clock-out
            ]);
            return response()->json([
                'message' => $employee->name . ' clocked in successfully.',
                'type' => 'clock_in',
                'employee_name' => $employee->name,
                'time' => Carbon::now()->format('H:i:s')
            ], 200);
        } elseif (!$attendance->clock_out) {
            // Clocked in but not out, so this is a clock-out
            $attendance->update([
                'clock_out' => Carbon::now(),
            ]);
            return response()->json([
                'message' => $employee->name . ' clocked out successfully.',
                'type' => 'clock_out',
                'employee_name' => $employee->name,
                'time' => Carbon::now()->format('H:i:s')
            ], 200);
        } else {
            // Already clocked in and out for today
            return response()->json([
                'message' => $employee->name . ' has already clocked in and out for today.',
                'type' => 'already_processed',
                'employee_name' => $employee->name
            ], 200); // 200 because the action itself was processed, just no new state change
        }
    }
}