@if ($item->type == 1)
    @include('Frontend.Checklist.partials.prestart_form')
@else
    @include('Frontend.Checklist.partials.service_form')
@endif