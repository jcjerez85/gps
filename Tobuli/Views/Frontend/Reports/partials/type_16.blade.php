@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        @if ( ! empty($item['table']))
            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('validation.attributes.date') }}</th>
                        <th>{{ trans('front.stop_duration') }}</th>
                        <th>{{ trans('front.idle_duration') }}</th>
                        <th>{{ trans('front.engine_work') }}</th>
                        <th>{{ trans('front.engine_hours') }}</th>
                        <th>{{ trans('front.travel_time') }}</th>
                        <th>{{ trans('front.overspeed') }}</th>
                        <th>{{ trans('front.distance_travelled') }}</th>
                        <th>{{ trans('front.travel_start_time') }}</th>
                        <th>{{ trans('front.travel_end_time') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($item['table']['rows'] as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['stop_duration'] }}</td>
                            <td>{{ $row['engine_idle'] }}</td>
                            <td>{{ $row['engine_work'] }}</td>
                            <td>{{ $row['engine_hours'] }}</td>
                            <td>{{ $row['drive_duration'] }}</td>
                            <td>{{ $row['overspeed_count'] }}</td>
                            <td>{{ $row['distance'] }}</td>
                            <td>{{ $row['first_drive_time'] }}</td>
                            <td>{{ $row['last_drive_time'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endforeach
@stop