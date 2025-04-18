@props(['placeholder' => 'Search...', 'btn' => 'btn--primary' , 'wiremodel' => 'searchTerm'])
<div class="input-group w-auto flex-fill">
    <input type="search" name="search"  wire:model.live="{{ $wiremodel }}" class="form-control bg--white" placeholder="{{ __($placeholder) }}" value="{{ request()->search }}">
    <button class="btn {{ $btn }}" type="submit"><i class="la la-search"></i></button>
</div>
