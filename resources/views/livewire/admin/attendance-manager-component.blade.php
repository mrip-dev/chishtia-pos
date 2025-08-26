<div>
    <div class="card p-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title">Weekly Attendance Overview</h4>
                <!-- Week Navigation -->
                <div class="btn-group" role="group">
                    <button wire:click="goToPreviousWeek" class="btn btn-secondary">&laquo; Prev</button>
                    <button wire:click="goToCurrentWeek" class="btn btn-primary">Today</button>
                    <button wire:click="goToNextWeek" class="btn btn-secondary">Next &raquo;</button>
                </div>
            </div>
            <p class="text-muted mt-2">
                Showing week: <strong>{{ $weekStartDate->format('M d, Y') }}</strong> to <strong>{{ $weekStartDate->copy()->endOfWeek()->format('M d, Y') }}</strong>
            </p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Staff Name</th>
                            @foreach ($dateRange as $date)
                                <th>
                                    {{ $date->format('D') }} <br>
                                    <small>{{ $date->format('d M') }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($staffWithAttendance as $staff)
                            <tr>
                                <td class="text-left">{{ $staff->name }}</td>
                                @foreach ($dateRange as $date)
                                    @php
                                        // Use the pre-structured data for a fast lookup
                                        $attendance = $staff->attendancesByDate->get($date->format('Y-m-d'));
                                    @endphp
                                    <td
                                        @if($attendance)
                                            wire:click="showDetails({{ $attendance->id }})"
                                            style="cursor: pointer;"
                                            title="Click for details"
                                        @endif
                                    >
                                        @if ($attendance)
                                            @php
                                                $status = $attendance->status;
                                                $badgeClass = 'badge bg-secondary';
                                                if ($status == 'present') $badgeClass = 'badge bg-success';
                                                elseif ($status == 'absent') $badgeClass = 'badge bg-danger';
                                                elseif ($status == 'late') $badgeClass = 'badge bg-warning';
                                                elseif ($status == 'on_leave') $badgeClass = 'badge bg-info';
                                                elseif ($status == 'half_day') $badgeClass = 'badge bg-primary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($dateRange) + 1 }}" class="text-center">No staff found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Attendance Details Modal -->
    @if ($selectedAttendance)
    <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attendance Details</h5>
                    <button type="button" class="close" wire:click="closeModal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Staff:</strong> {{ $selectedAttendance->admin->name }}</p>
                    <p><strong>Date:</strong> {{ Carbon\Carbon::parse($selectedAttendance->date)->format('l, F j, Y') }}</p>
                    <p><strong>Status:</strong> <span class="badge badge bg-primary">{{ ucfirst($selectedAttendance->status) }}</span></p>
                    <hr>
                    <p><strong>Check-in:</strong> {{ $selectedAttendance->check_in ? $selectedAttendance->check_in->format('h:i:s A') : 'N/A' }}</p>
                    <p><strong>Check-out:</strong> {{ $selectedAttendance->check_out ? $selectedAttendance->check_out->format('h:i:s A') : 'N/A' }}</p>
                     @if($selectedAttendance->check_in && $selectedAttendance->check_out)
                        <p><strong>Total Hours:</strong> {{ $selectedAttendance->check_in->diff($selectedAttendance->check_out)->format('%h hours, %i minutes') }}</p>
                    @endif
                    <p><strong>Remarks:</strong> {{ $selectedAttendance->remarks ?? 'No remarks.' }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Backdrop -->
    <div class="modal-backdrop fade show"></div>
    @endif
</div>