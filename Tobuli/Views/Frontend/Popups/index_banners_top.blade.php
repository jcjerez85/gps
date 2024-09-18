@if (Auth::check())
    @foreach(Auth::user()->topBars() as $topBar)
        <div class="banner" id="banner-{{ $topBar->id }}">
            {!! $topBar->content !!}
        </div>
    @endforeach

    @foreach(Auth::user()->filteredUnreadNotifications(['data.position' => 'top']) as $notification)
        <div class="banner" id="banner-{{ $notification->data['id'] }}">
            {!! $notification->data['title'] !!}
        </div>
    @endforeach
@endif
