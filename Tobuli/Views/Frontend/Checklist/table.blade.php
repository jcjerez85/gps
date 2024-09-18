@if (isset($checklists) && count($checklists))
    @foreach ($checklists as $item)
        @include('Frontend.Checklist.partials.form')
    @endforeach
@else
    <div class="panel">{!!trans('admin.no_data')!!}</div>
@endif

<div class="nav-pagination">
    @if (isset($checklists) && count($checklists))
        {!! $checklists->setPath(route('checklists.table'))->appends(['service_id' => $service_id])->render() !!}
    @endif
</div>

<script>
    $(document).ready(function() {
        $checklist.showSignatureFields();
    });
</script>
