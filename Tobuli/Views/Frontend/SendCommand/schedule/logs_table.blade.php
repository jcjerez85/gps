<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('global.device') }}</th>
            <th>{{ trans('front.connection') }}</th>
            <th>{{ trans('validation.attributes.status') }}</th>
            <th>{{ trans('front.response') }}</th>
            <th>{{ trans('front.time') }}</th>
        </tr>
        </thead>
        <tbody>
        @if(count($logs->getCollection()) < 1)
            <tr class="text-center bg-warning">
                <td colspan="5">{{ trans('front.nothing_found_request') }}</td>
            </tr>
        @else
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->device->name }}</td>
                    <td>{{ $log->connection }}</td>
                    <td>{{ $log->status == 0 ? 'failed' : 'success' }}</td>
                    <td>{{ $log->response }}</td>
                    <td>{{ Formatter::time()->human($log->created_at) }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $logs->setPath(route('command_schedules.logs', ['id' => $command_schedule->id]))->render() !!}
</div>