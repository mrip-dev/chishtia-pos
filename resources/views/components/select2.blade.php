@props([
    'dataArray',
    'placeholder' => 'Select',
    'id',
    'multiple' => false,
    'defer' => true
])

<div
    wire:ignore
    x-data="{
        count: 0,
        initializeSelect2() {
            const select = $refs['{{ $id }}'];

            if (this.count > 0) {
                $(select).select2('destroy');
            }

            let initialOptions = [];
            let selectedValue = '';

            if (@this) {
                initialOptions = @this.get('{{ $dataArray }}') ?? [];
                selectedValue = @this.get('{{ $attributes->whereStartsWith('wire:model')->first() }}');
            }

            $(select).select2({
                data: initialOptions,
                placeholder: '{{ $placeholder }}',
                allowClear: true,
                width: '100%',
            });

            $(select).val(selectedValue).trigger('change');

            if (this.count < 1) {
                $(select).on('select2:select select2:unselect', () => {
                    const selected = $(select).val();
                    @this.set('{{ $attributes->whereStartsWith('wire:model')->first() }}', selected, {{ $defer ? 'true' : 'false' }});
                });

                $(select).on('select2:clear', () => {
                    @this.set('{{ $attributes->whereStartsWith('wire:model')->first() }}', null, {{ $defer ? 'true' : 'false' }});
                });

                this.count++;
            }
        }
    }"
    x-init="initializeSelect2()"
    x-on:re-init-select-2-component.window="initializeSelect2()"
>
    <select
        {{ $attributes }}
        id="{{ $id }}"
        x-ref="{{ $id }}"
        data-placeholder="{{ $placeholder }}"
        @if($multiple) multiple="multiple" @endif
    ></select>
</div>
