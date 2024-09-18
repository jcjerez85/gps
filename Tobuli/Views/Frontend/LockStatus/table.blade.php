<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('front.time') !!}
            {!! tableHeader('front.duration') !!}
            {!! tableHeader('front.location') !!}
        </tr>
        </thead>
        <tbody>
        @if (! empty($data))
            @foreach ($data as $row)
                <tr>
                    <td>
                        {{ $row['time'] }}
                    </td>
                    <td>
                        {{ $row['duration'] }}
                    </td>
                    <td>
                        {!! googleMapLink($row['lat'],$row['lng']) !!}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="5">{!! trans('admin.no_data') !!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    @if (isset($data) && count($data))
        {!! $data->setPath(route('lock_status.table', [$deviceId]))->render() !!}
    @endif
</div>
