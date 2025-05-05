<div>
    <div class="card">
        <div class="card-header">
            <h3 class="text-center ">Day Book</h3>

        </div>
    <div class="card-body">
        <table class="table table-striped ">
            <thead class="bg--primary">
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Opening Balance</th>
                    <th>Closing Balance</th>
                    <th>Action(s)</th>
                </tr>
            </thead>
            <tbody>

                @forelse ($dailyBooks as $dailyBook)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $dailyBook->date }}</td>
                    <td class="text-success"> {{ $this->getOpeningBalance($dailyBook->date) ?? 0 }}</td>
                    <td class="text-danger">{{ $this->getClosingBalance($dailyBook->date) ?? 0 }}</td>
                    <td>
                        <a href="{{ route('admin.daybook.detail', $dailyBook->date) }}">
                            <i class="fas fa-eye text-info cursor-pointer"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No Data Found</td>
                </tr>
            @endforelse


            </tbody>
        </table>
     </div>
</div>
</div>
