<div class="container text-center" style="max-width: 500px; margin-top: 2vh;">
    <div class="card">
        <div class="card-header">
            <h3>Moeen Traders - Staff Attendance</h3>
        </div>
        <div class="card-body">
            <h4>Welcome, {{ $staff->name }}</h4>

            <!-- Live Clock -->
            <div wire:poll.1s>
                <h5>{{ \Carbon\Carbon::parse($currentTime)->format('l, F j, Y') }}</h5>
                <h1 class="display-4 font-weight-bold">{{ \Carbon\Carbon::parse($currentTime)->format('h:i:s A') }}</h1>
            </div>

            <hr>

            <!-- Status Message -->
            @if ($message)
                <div class="alert alert-{{ $messageType }}">{{ $message }}</div>
            @endif

            <!-- Attendance Status -->
            @if ($todaysAttendance && $todaysAttendance->check_in)
                <p class="text-success">
                    Clocked In at: <strong>{{ $todaysAttendance->check_in->format('h:i A') }}</strong>
                </p>
                @if ($todaysAttendance->check_out)
                    <p class="text-info">
                        Clocked Out at: <strong>{{ $todaysAttendance->check_out->format('h:i A') }}</strong>
                    </p>
                @endif
            @else
                <p class="text-muted">You are currently clocked out.</p>
            @endif

            <div class="mt-4">
                <!-- Logic to show the correct button -->
                @if (!$todaysAttendance || !$todaysAttendance->check_in)
                    <!-- SIMULATOR BUTTON -->
                    <button wire:click="clockIn" class="btn btn-success btn-lg btn-block">
                        <i class="fas fa-fingerprint"></i> Clock In
                    </button>
                @elseif (!$todaysAttendance->check_out)
                     <!-- SIMULATOR BUTTON -->
                    <button wire:click="clockOut" class="btn btn-danger btn-lg btn-block">
                        <i class="fas fa-fingerprint"></i> Clock Out
                    </button>
                @else
                    <p class="font-weight-bold">Your attendance for today is complete.</p>
                @endif
            </div>

        </div>
        <div class="card-footer text-muted">
            This system records your IP address and device information on clock-in/out.
        </div>
    </div>
</div>