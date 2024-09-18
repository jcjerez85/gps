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
                        @if ( ! empty($item['table']['rows'][0]['fuel_level_start_list']))
                            @foreach($item['table']['rows'][0]['fuel_level_start_list'] as $row)
                                <th>{{ $row['title'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['rows'][0]['fuel_level_end_list']))
                            @foreach($item['table']['rows'][0]['fuel_level_end_list'] as $row)
                                <th>{{ $row['title'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['rows'][0]['fuel_consumption_list']))
                            @foreach($item['table']['rows'][0]['fuel_consumption_list'] as $row)
                                <th>{{ $row['title'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['rows'][0]['fuel_avg_list']))
                            @foreach($item['table']['rows'][0]['fuel_avg_list'] as $row)
                                <th>{{ $row['title'] }}</th>
                            @endforeach
                        @endif
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
                            @if ( ! empty($row['fuel_level_start_list']))
                                @foreach($row['fuel_level_start_list'] as $_row)
                                    <td>{{ $_row['value'] }}</td>
                                @endforeach
                            @endif
                            @if ( ! empty($row['fuel_level_end_list']))
                                @foreach($row['fuel_level_end_list'] as $_row)
                                    <td>{{ $_row['value'] }}</td>
                                @endforeach
                            @endif
                            @if ( ! empty($row['fuel_consumption_list']))
                                @foreach($row['fuel_consumption_list'] as $_row)
                                    <td>{{ $_row['value'] }}</td>
                                @endforeach
                            @endif
                            @if ( ! empty($row['fuel_avg_list']))
                                @foreach($row['fuel_avg_list'] as $_row)
                                    <td>{{ $_row['value'] }}</td>
                                @endforeach
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th>{{ $item['table']['totals']['stop_duration'] }}</th>
                        <th>{{ $item['table']['totals']['engine_idle'] }}</th>
                        <th>{{ $item['table']['totals']['engine_work'] }}</th>
                        <th>{{ $item['table']['totals']['engine_hours'] }}</th>
                        <th>{{ $item['table']['totals']['drive_duration'] }}</th>
                        <th>{{ $item['table']['totals']['overspeed_count'] }}</th>
                        <th>{{ $item['table']['totals']['distance'] }}</th>
                        <th></th>
                        <th></th>
                        @if ( ! empty($item['table']['totals']['fuel_level_start_list']))
                            @foreach($item['table']['totals']['fuel_level_start_list'] as $_row)
                                <th>{{ $_row['value'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['totals']['fuel_level_end_list']))
                            @foreach($item['table']['totals']['fuel_level_end_list'] as $_row)
                                <th>{{ $_row['value'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['totals']['fuel_consumption_list']))
                            @foreach($item['table']['totals']['fuel_consumption_list'] as $_row)
                                <th>{{ $_row['value'] }}</th>
                            @endforeach
                        @endif
                        @if ( ! empty($item['table']['totals']['fuel_avg_list']))
                            @foreach($item['table']['totals']['fuel_avg_list'] as $_row)
                                <th>{{ $_row['value'] }}</th>
                            @endforeach
                        @endif
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
    @endforeach
@stop