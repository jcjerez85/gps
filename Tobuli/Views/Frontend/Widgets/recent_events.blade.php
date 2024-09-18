<div class="widget widget-recent-events" id="widget-recent-events" data-id="{{ $device->id ?? '' }}">
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon event"></i> {{ trans('front.recent_events') }}
        </div>
    </div>

    <div class="widget-body">
        @if (isset($recent_events) && count($recent_events))
            <table class="table">
                <tbody>
                    @foreach ($recent_events as $event)
                        <tr onclick="app.events.select({{ $event->id }});">
                            <td>{{ $event->timePassed }}</td>
                            <td rel="tooltip" data-placement="top" title="{!! $event->title !!}">{{ $event->title }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div class="widget-empty">
            <p>{{ trans('front.no_events') }}</p>
        </div>
    </div>
</div>
