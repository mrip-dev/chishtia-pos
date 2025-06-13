@props([
    'dataArray' => [],
    'placeholder' => 'Select',
    'id',
    'multiple' => false,
    'selected' => null, // Optional default selected value
])

<select
    id="{{ $id }}"
    class="form-control"
    data-placeholder="{{ $placeholder }}"
    @if($multiple) multiple="multiple" @endif
>
    <option></option> <!-- Required for allowClear -->
   @foreach(is_array($dataArray) ? $dataArray : [] as $item)

        <option value="{{ $item['id'] }}" {{ (collect($selected)->contains($item['id'])) ? 'selected' : '' }}>
            {{ $item['text'] }}
        </option>
    @endforeach
</select>
