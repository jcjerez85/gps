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
                                <th>{{ trans('front.ignition_on_off') }}</th>
                                <th>{{ trans('front.event_time') }}</th>
                                <th>{{ trans('front.speed') }}</th>
                                <th>{{ trans('front.trip_distance') }}</th>
                                <th>{{ trans('front.engine_work') }}</th>
                                <th>{{ trans('front.stopped_for') }}</th>
                                <th>{{ trans('front.driver') }}</th>
                                <th>{{ trans('front.location') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                @if ($row['group_key'] == 'date')
                                    <tr>
                                        <td><strong>{{ $row['date'] }}</strong></td>
                                        <td colspan="7"></td>
                                    </tr>
                                @elseif ($row['group_key'] == 'engine_on')
                                    <tr>
                                        <td>{{ trans('front.on') }}</td>
                                        <td>{{ $row['time'] }}</td>
                                        <td>{{ $row['speed_avg'] }}</td>
                                        <td>{{ $row['distance'] }}</td>
                                        <td>{{ $row['duration'] }}</td>
                                        <td></td>
                                        <td>{{ $row['drivers'] }}</td>
                                        <td>{!! $row['location'] !!}</td>
                                    </tr>
                                @elseif ($row['group_key'] == 'engine_off')
                                    <tr>
                                        <td>{{ trans('front.off') }}</td>
                                        <td>{{ $row['time'] }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $row['duration'] }}</td>
                                        <td></td>
                                        <td>{!! $row['location'] !!}</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @include('Frontend.Reports.partials.item_total')
            @endif
        </div>
    @endforeach
@stop