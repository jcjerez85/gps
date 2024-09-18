@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        @foreach($item['meta'] as $meta)
                            <th>{{ $meta['title'] }}</th>
                        @endforeach
                        <th>{{ trans('front.server_time') }}</th>
                        <th>{{ trans('front.time') }}</th>
                        <th>{{ trans('front.latitude') }}</th>
                        <th>{{ trans('front.longitude') }}</th>
                        <th>{{ trans('front.address') }}</th>
                        <th>{{ trans('front.altitude') }}</th>
                        <th>{{ trans('front.speed') }}</th>

                        @foreach($item['sensors'] as $sensor)
                            <th>{{ $sensor['name'] }}</th>
                        @endforeach

                        @foreach($item['parameters'] as $parameter)
                            <th>{{ $parameter }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>

                    @if (isset($item['error']))
                        <tr>
                            <td colspan="{{ 7 + count($item['meta']) + count($item['sensors']) + count($item['parameters']) }}">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['server_time'] }}</td>
                                <td>{{ $row['time'] }}</td>
                                <td>{{ $row['latitude'] }}</td>
                                <td>{{ $row['longitude'] }}</td>
                                <td>{!! $row['location'] !!}</td>
                                <td>{{ $row['altitude'] }}</td>
                                <td>{{ $row['speed'] }}</td>
                                @foreach($item['sensors'] as $sensor)
                                    <th>{{ $row['sensors'][$sensor['id']] ?? '' }}</th>
                                @endforeach
                                @foreach($item['parameters'] as $parameter)
                                    <td>{{ $row['parameters'][$parameter] ?? '' }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop