<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            // This is the unique ID you give to each staff member for the device
            'staff_fingerprint_id' => 'required|string',
        ]);

        // 2. Find the staff member using that ID
        $staff = Admin::where('fingerprint_user_id', $validated['staff_fingerprint_id'])->first();

        if (!$staff) {
            return response()->json(['message' => 'Staff member not found.'], 404);
        }

        // 3. Check their current attendance status
        $attendance = Attendance::where('admin_id', $staff->id)->where('date', today())->first();

        // Check for Clock In
        if (!$attendance || !$attendance->check_in) {
            $now = now();
            $startTime = today()->setTime(9, 5, 0);
            $status = $now->gt($startTime) ? 'late' : 'present';

            Attendance::updateOrCreate(
                ['admin_id' => $staff->id, 'date' => today()],
                ['check_in' => $now, 'status' => $status]
            );

            return response()->json([
                'message' => 'Clock In Successful!',
                'staff_name' => $staff->name,
                'time' => $now->format('h:i A'),
            ]);
        }

        // Check for Clock Out
        if (!$attendance->check_out) {
            $now = now();
            $attendance->update(['check_out' => $now]);

            return response()->json([
                'message' => 'Clock Out Successful!',
                'staff_name' => $staff->name,
                'time' => $now->format('h:i A'),
            ]);
        }

        // If already clocked in and out
        return response()->json(['message' => 'Attendance already complete for today.'], 400);
    }
}