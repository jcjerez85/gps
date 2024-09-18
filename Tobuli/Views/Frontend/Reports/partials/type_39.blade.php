@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else
                @if ( ! empty($item['table']['rows']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('front.position_a') }}</th>
                                <th>{{ trans('front.leave') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('front.route_length') }}</th>
                                <th>{{ trans('front.position_b') }}</th>
                                <th>{{ trans('front.end') }}</th>
                                <th>{{ trans('front.time_at_location') }}</th>
                                <th>{{ trans('front.departure_time') }}</th>
                                <th>{{ trans('front.average_speed') }}</th>
                                <th>{{ trans('front.top_speed') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach(array_chunk($item['table']['rows'], 2) as $chunk)
                                <tr>
                                    <td>{!! $chunk[0]['location_start'] !!}</td>
                                    <td>{{ $chunk[0]['start_at'] }}</td>
                                    <td>{{ $chunk[0]['duration'] }}</td>
                                    <td>{{ $chunk[0]['distance'] }}</td>
                                    <td>{!! $chunk[0]['location_end'] !!}</td>
                                    <td>{{ $chunk[0]['end_at'] }}</td>
                                    <td>{{ \Illuminate\Support\Arr::get($chunk, '1.duration') }}</td>
                                    <td>{{ \Illuminate\Support\Arr::get($chunk, '1.end_at') }}</td>
                                    <td>{{ $chunk[0]['speed_avg'] }}</td>
                                    <td>{{ $chunk[0]['speed_max'] }}</td>
                                </tr>
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