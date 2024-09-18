@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                        <th>{{ trans('global.date') }} ({{ trans('front.zone_in') }})</th>
                        <th>{{ trans('front.time') }} ({{ trans('front.zone_in') }})</th>
                        <th>{{ trans('global.date') }} ({{ trans('front.zone_out') }})</th>
                        <th>{{ trans('front.time') }} ({{ trans('front.zone_out') }})</th>
                        <th>{{ trans('front.stop_duration') }}</th>
                        <th>{{ trans('global.distance') }}</th>
                        <th>{{ trans('validation.attributes.geofence_name') }}</th>
                        <th>{{ trans('front.position') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="8">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach ($item['table']['rows'] as $row)
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td>{{ $row['start_date_at'] }}</td>
                            <td>{{ $row['start_time_at'] }}</td>
                            <td>{{ $row['end_date_at'] }}</td>
                            <td>{{ $row['end_time_at'] }}</td>
                            <td>{{ $row['stop_duration'] }}</td>
                            <td>{{ $row['drive_distance'] }}</td>
                            <td>{{ $row['group_geofence'] }}</td>
                            <td>{!! $row['location'] !!}</td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop