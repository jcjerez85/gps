<div class="btn-group dropdown droparrow" data-position="fixed">
    <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
    <ul class="dropdown-menu">
        @foreach($actions as $action)
            @include('front::Lookup.partials.action', $action)
        @endforeach
    </ul>
</div>
