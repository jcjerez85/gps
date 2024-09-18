<div id="dashboard_blocks" class="row">
    @if( ! empty($blocks))
        @foreach($blocks as $block => $html)
            {!! $html !!}
        @endforeach
    @else
        <p><b>{{ trans('front.nothing_found_request') }}</b></p>
    @endif
</div>