<div>

    @include('livewire.admin.manufacturing.partials.filters')

    @if (!$showDetails)
    @include('livewire.admin.manufacturing.partials.index')
    @else
    @if ($showType== 'raw')
    @include('livewire.admin.manufacturing.partials.raw')
    @elseif($showType== 'refined')
    @include('livewire.admin.manufacturing.partials.refined')
    @endif
    @endif
    @push('script')


    <script>
        (function($) {
            "use strict";
            window.addEventListener('confirmNewFlow', event => {
                console.log(event);
                Swal.fire({
                    title: 'Are You Sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, create it!',
                    cancelButtonText: 'No, cancel!',

                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.createFlow();
                    }
                });
            })



        })(jQuery);
    </script>
    @endpush

</div>