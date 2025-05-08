@props([
'dataArray',
'placeholder' => 'Select',
'id',
'multiple' => false,
'defer' => true,
'allowAdd' => false,
])

<div
    wire:ignore
    x-data="{
        count: 0,
        allowAdd: {{ $allowAdd ? 'true' : 'false' }},
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
                tags: this.allowAdd,
                width: '100%',
            });

            $(select).val(selectedValue).trigger('change');

            if (this.count < 1) {
            if (this.allowAdd) {
                $(select).on('select2:select', (e) => {
                    const selected = $(select).val();
                    const newItem = e.params.data;

                    @this.set('{{ $attributes->whereStartsWith('wire:model')->first() }}', selected, {{ $defer ? 'true' : 'false' }});

                    // if the item is new (not from existing data array)
                    if (newItem.id === newItem.text) {
                        Livewire.dispatch('addNewSelectOption', {
                            text: newItem.text,
                            model: '{{ $attributes->whereStartsWith('wire:model')->first() }}',
                            list: '{{ $dataArray }}'
                        });
                    }
                });
               }
                $(select).on('select2:unselect', () => {
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
    x-on:re-init-select-2-component.window="initializeSelect2()">
    <select
        {{ $attributes }}
        id="{{ $id }}"
        x-ref="{{ $id }}"
        data-placeholder="{{ $placeholder }}"
        @if($multiple) multiple="multiple" @endif></select>
</div>