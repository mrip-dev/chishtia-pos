@props([
    // The name of the Livewire property to bind to.
    'wireModel' => null,
    'name' => 'date',
    'value' => '',
    'placeholder' => 'Select a Date',
    'id' => 'datepicker-' . uniqid(),
])

<div
    wire:ignore
    x-data="{
        // Entangle the 'value' with the Livewire model. Use .live for immediate updates.
        @if ($wireModel)
            value: @entangle($wireModel).live,
        @else
            value: '{{ $value }}',
        @endif

        instance: null, // To hold the picker instance

        init() {
            // Only initialize if the component is actually wired to a model.
            if (!'{{ $wireModel }}') return;

            this.instance = $(this.$refs.input).daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                // **IMPROVEMENT 1: Let the library update the input for reliability.**
                autoUpdateInput: true,
                locale: {
                    format: 'DD-MM-YYYY',
                    cancelLabel: 'Clear',
                }
            });

            // If the initial value is empty, clear the input field.
            if (!this.value) {
                $(this.$refs.input).val('');
            }

            // Sync the picker with the initial value
            this.updatePickerFromValue(this.value);

            // Watch for external changes (e.g., Livewire parent resets the value)
            this.$watch('value', (newValue) => {
                this.updatePickerFromValue(newValue);
            });

            // On apply, just update the model. The library handles the input text.
            $(this.$refs.input).on('apply.daterangepicker', (e, picker) => {
                this.value = picker.startDate.format('DD-MM-YYYY');
            });

            // On cancel, clear both the input and the model.
            $(this.$refs.input).on('cancel.daterangepicker', (e, picker) => {
                $(this.$refs.input).val('');
                this.value = null;
            });
        },

        updatePickerFromValue(dateString) {
            const picker = $(this.$refs.input).data('daterangepicker');
            if (!picker) return;

            if (!dateString) {
                $(this.$refs.input).val('');
                return;
            }

            let date = moment(dateString, 'DD-MM-YYYY');
            if (date.isValid()) {
                picker.setStartDate(date);
                picker.setEndDate(date);
            }
        }
    }"
>
    <div class="input-group w-auto flex-fill">
        <input
            x-ref="input"
            type="text"
            id="{{ $id }}"
            name="{{ $name }}"
            class="form-control bg--white pe-2"
            placeholder="{{ __($placeholder) }}"
            autocomplete="off"
        >
        <button type="button" class="btn btn--primary input-group-text">
            <i class="la la-calendar"></i>
        </button>
    </div>
</div>

{{-- These library includes remain correct --}}
@pushOnce('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endPushOnce

@pushOnce('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endPushOnce