<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Assigns a simulated fingerprint ID to an employee.
     * This would ideally be a more complex process with a real device.
     */
    public function enrollFingerprint(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'fingerprint_id' => [
                'required',
                'string',
                'min:3',
                Rule::unique('employees', 'fingerprint_id')->ignore($request->employee_id, 'id'),
            ],
        ]);

        try {
            $employee = Admin::find($request->employee_id);
            $employee->fingerprint_id = $request->fingerprint_id;
            $employee->save();

            return response()->json([
                'message' => 'Fingerprint ID assigned successfully.',
                'employee' => $employee
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error assigning fingerprint ID.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Checks if a given fingerprint ID already exists.
     * Useful for real-time validation during enrollment.
     */
    // In EmployeeController.php

    /**
     * Retrieves employee details by fingerprint ID.
     */
    public function getEmployeeByFingerprintId(Request $request)
    {
        $request->validate([
            'fingerprint_id' => 'required|string',
        ]);

        $employee = Admin::where('fingerprint_id', $request->fingerprint_id)->first();

        if ($employee) {
            $today = Carbon::today();
            $attendance = Attendance::where('admin_id', $employee->id)
                ->whereDate('date', $today)
                ->first();
            if (!$attendance) {
                // No record for today, so this is a clock-in
                Attendance::create([
                    'admin_id' => $employee->id,
                    'date' => $today,
                    'check_in' => Carbon::now(),
                    'status' => 'present', // Or 'Pending' until clock-out
                ]);
                return response()->json([
                    'message' => $employee->name . ' clocked in successfully.',
                    'type' => 'clock_in',
                    'employee' => $employee,
                    'time' => Carbon::now()->format('H:i:s')
                ], 200);
            } elseif (!$attendance->clock_out) {
                // Clocked in but not out, so this is a clock-out
                $attendance->update([
                    'check_out' => Carbon::now(),
                ]);
                return response()->json([
                    'message' => $employee->name . ' clocked out successfully.',
                    'type' => 'clock_out',
                    'employee' => $employee,
                    'time' => Carbon::now()->format('H:i:s')
                ], 200);
            } else {
                // Already clocked in and out for today
                return response()->json([
                    'message' => $employee->name . ' has already clocked in and out for today.',
                    'type' => 'already_processed',
                    'employee' => $employee
                ], 200); // 200 because the action itself was processed, just no new state change
            }
        } else {
            return response()->json([
                'message' => 'Employee not found with this fingerprint ID.',
                'employee' => null
            ], 404); // Use 404 for "not found"
        }
    }
}
