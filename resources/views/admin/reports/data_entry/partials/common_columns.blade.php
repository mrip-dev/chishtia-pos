<td> {{ $entry->action_name }}</td>

<td>
    {{-- permit($entry->admin->role_id != 0)
        <a href="{{ route('admin.staff.index') }}?search={{ $entry->admin->username }}">
            {{ $entry->admin->username }}
        </a>
    @else --}}
    {{ $entry->admin->username }}
    {{-- @endpermit --}}
</td>

<td> {{ showDateTime($entry->created_at) }}</td>
