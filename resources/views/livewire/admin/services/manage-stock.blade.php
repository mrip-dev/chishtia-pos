<div>
    @include('livewire.admin.services.partials.stock-inout-filters')

    @if (!$isCreating && !$showDetails)
    @include('livewire.admin.services.partials.stock-inout-index')

    @else
    @if($showDetails && $selectedStock)
    @include('livewire.admin.services.partials.stock-inout-detail')
    @else
    @include('livewire.admin.services.partials.stock-inout-create')
    @endif
    @endif
    @push('script')
    <script>
        (function($) {
            "use strict";
            window.addEventListener('openPaymentModal', event => {
                $('#paymentModal').modal('show');
            })

            window.addEventListener('closePaymentModal', event => {
                $('#paymentModal').modal('hide');
            })

        })(jQuery);
    </script>
    @endpush

</div>