<div class="container mt-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="Search banks...">
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-outline-custom" data-bs-toggle="modal" data-bs-target="#cuModal" wire:click="addNew">
                + Add New Bank
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table-bordered table-custom">
                            <thead class="table table--light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($banks as $bank)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $bank->name }}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm"
                                                wire:click="editItem({{ $bank->id }})"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cuModal">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm"
                                                wire:click="deleteItem({{ $bank->id }})"
                                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No Banks Found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create & Update Modal -->
    <div class="modal fade" id="cuModal" tabindex="-1" aria-labelledby="cuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $bank->id ? 'Edit Bank' : 'Add Bank' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form wire:submit.prevent="addEntry">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" wire:model.defer="bank.name" class="form-control" required>
                            @error('bank.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-custom w-100">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .btn-outline-custom {
            color: #97ca9c;
            border-color: #97ca9c;
            background-color: transparent;
        }

        .btn-outline-custom:hover {
            color: white;
            background-color: #97ca9c;
            border-color: #97ca9c;
        }

        .btn-custom {
            background-color: #97ca9c;
            color: white;
            border: none;
        }

        .btn-custom:hover {
            background-color: #86b890;
        }
        .table-custom thead {
        background-color: #97ca9c !important; /* Custom header background */
        color: white !important; /* White text for contrast */
    }
    </style>

    <!-- Livewire Scripts -->
    <script>
        window.addEventListener('close-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('cuModal'));
            modal.hide();
        });

        window.addEventListener('success-notification', event => {
            Swal.fire({
                title: 'Success!',
                text: event.detail.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        });
    </script>
</div>
