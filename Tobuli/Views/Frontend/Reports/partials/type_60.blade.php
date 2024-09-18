@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas('device') as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('global.date') }}</th>
                    <th>{{ trans('validation.attributes.shift_start') }}</th>
                    <th>{{ trans('front.start_time') }}</th>
                    <th>{{ trans('front.geofence') }}</th>
                    <th>{{ trans('front.zone_in') }}</th>
                    <th>{{ trans('front.zone_out') }}</th>
                    <th>{{ trans('front.end_time') }}</th>
                    <th>{{ trans('front.daily_cleaning_distance') }} ({{ Formatter::distance()->getUnit() }})</th>
                    <th>{{ trans('front.daily_transport_distance') }} ({{ Formatter::distance()->getUnit() }})</th>
                    <th>{{ trans('front.work_hours_daily') }}</th>
                    <th>{{ trans('front.monthly_cleaning_distance') }} ({{ Formatter::distance()->getUnit() }})</th>
                    <th>{{ trans('front.active_days') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)

                    @if (!empty($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="12">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach ($item['table']['rows'] as $row)
                            @if(!empty($row['error']))
                                <tr>
                                    @foreach($item['meta'] as $key => $meta)
                                        <td>{{ $meta['value'] }}</td>
                                    @endforeach
                                    <td>{{ $row['date'] }}</td>
                                    <td colspan="11">{{ $row['error'] }}</td>
                                </tr>
                            @elseif (empty($row['geofences']))
                                <tr>
                                    @foreach($item['meta'] as $key => $meta)
                                        <td>{{ $meta['value'] }}</td>
                                    @endforeach
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['shift_start'] }}</td>
                                    <td>{{ $row['start_time'] }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $row['end_time'] }}</td>
                                    <td>{{ $row['speed_below_distance'] }}</td>
                                    <td>{{ $row['speed_above_distance'] }}</td>
                                    <td>{{ $row['speed_below_duration'] }}</td>
                                    <td>{{ $item['table']['totals']['speed_below_distance'] }}</td>
                                    <td>{{ $item['table']['totals']['active_days'] }}</td>
                                </tr>
                            @else
                                @foreach ($row['geofences'] as $geofence)
                                    <tr>
                                        @foreach($item['meta'] as $key => $meta)
                                            <td>{{ $meta['value'] }}</td>
                                        @endforeach
                                        <td>{{ $row['date'] }}</td>
                                        <td>{{ $row['shift_start'] }}</td>
                                        <td>{{ $row['start_time'] }}</td>
                                        <td>{{ $geofence['name'] }}</td>
                                        <td>{{ $geofence['enter_time'] }}</td>
                                        <td>{{ $geofence['leave_time'] }}</td>
                                        <td>{{ $row['end_time'] }}</td>
                                        <td>{{ $row['speed_below_distance'] }}</td>
                                        <td>{{ $row['speed_above_distance'] }}</td>
                                        <td>{{ $row['speed_below_duration'] }}</td>
                                        <td>{{ $item['table']['totals']['speed_below_distance'] }}</td>
                                        <td>{{ $item['table']['totals']['active_days'] }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop