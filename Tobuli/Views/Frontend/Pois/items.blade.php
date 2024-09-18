<ul class="group-list">
    @foreach ($items as $key => $item)
        @include('front::Pois.item')
    @endforeach
</ul>

@if ($items->nextPageUrl())
    <form onSubmit="app.pois.listPage('{!! $items->nextPageUrl() !!}', this); return false;" class="text-center">
        <button class="btn btn-default btn-xs">
            <i class="fa fa-refresh"></i> {{ trans('front.show_more') }}
        </button>
    </form>
@endif