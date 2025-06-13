<div>
    <x-select2
        name="{{$name}}"
        id="{{ $selectid }}"
        dataArray="options"
        wire:model="name"
        placeholder="{{ $placeholder }}"
        :allowAdd="false" />
</div>