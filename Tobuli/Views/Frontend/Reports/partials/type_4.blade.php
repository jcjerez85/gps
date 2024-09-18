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
                                <th>{{ trans('validation.attributes.date') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('front.position_a') }}</th>
                                <th>{{ trans('front.position_b') }}</th>
                                <th>{{ trans('front.route_length') }}</th>
                                <th>{{ trans('front.driver') }}</th>
                                @if ( ! empty($item['table']['rows'][0]['fuel_consumption_list']))
                                    @foreach($item['table']['rows'][0]['fuel_consumption_list'] as $row)
                                    <th>{{ $row['title'] }}</th>
                                    @endforeach
                                @endif
                                @if ( ! empty($item['table']['rows'][0]['fuel_price_list']))
                                    @foreach($item['table']['rows'][0]['fuel_price_list'] as $row)
                                        <th>{{ $row['title'] }}</th>
                                    @endforeach
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{!! $row['location_start'] !!}</td>
                                    <td>{!! $row['location_end'] !!}</td>
                                    <td>{{ $row['distance'] }}</td>
                                    <td>{{ $row['drivers'] }}</td>

                                    @if ( ! empty($row['fuel_consumption_list']))
                                        @foreach($row['fuel_consumption_list'] as $_row)
                                            <td>{{ $_row['value'] }}</td>
                                        @endforeach
                                    @endif
                                    @if ( ! empty($row['fuel_price_list']))
                                        @foreach($row['fuel_price_list'] as $_row)
                                            <td>{{ $_row['value'] }}</td>
                                        @endforeach
                                    @endif

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