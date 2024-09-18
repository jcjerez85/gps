@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else
                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('validation.attributes.geofence_name') }}</th>
                                <th>{{ trans('front.zone_in') }}</th>
                                <th>{{ trans('front.zone_out') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('front.route_length') }}</th>
                                <th>{{ trans('front.top_speed') }}</th>
                                <th>{{ trans('front.average_speed') }}</th>
                                <th>{{ trans('front.average_speed') }} (d/t)</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['group_geofence'] }}</td>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['end_at'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                    <td>{{ $row['speed_max'] }}</td>
                                    <td>{{ $row['speed_avg'] }}</td>
                                    <td>{{ $row['speed_avg_calc'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $item['table']['totals']['duration'] }}</td>
                                    <td>{{ $item['table']['totals']['distance'] }}</td>
                                    <td>{{ $item['table']['totals']['speed_max'] }}</td>
                                    <td>{{ $item['table']['totals']['speed_avg'] }}</td>
                                    <td>{{ $item['table']['totals']['speed_avg_calc'] }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop