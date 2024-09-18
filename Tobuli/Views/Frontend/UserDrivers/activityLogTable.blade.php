<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('global.device') }}</th>
            <th>{{ trans('front.start') }}</th>
            <th>{{ trans('front.end') }}</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->device }}</td>
                    <td>{{ Formatter::time()->human($log->start) }}</td>
                    <td>{{ Formatter::time()->human($log->end) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">{!! trans('front.nothing_found_request') !!}</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if (!$logs->isEmpty())
    <div class="nav-pagination">
        {!! $logs->setPath(route('user_drivers.activity_log', [$driver->id, 'table']))->render() !!}
    </div>
@endif