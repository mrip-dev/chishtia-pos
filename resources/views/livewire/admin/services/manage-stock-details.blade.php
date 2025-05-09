<div>
    @if($modelType == 'client')
     @include('livewire.admin.services.partials.service-stock-clients')
    @else
     @include('livewire.admin.services.partials.service-stock-details')
    @endif
</div>