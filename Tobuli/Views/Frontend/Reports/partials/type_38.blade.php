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
                    <th>{{ trans('front.time') }}</th>
                    <th>{{ trans('front.position') }}</th>
                    <th>{{ trans('front.speed') }}</th>
                    <th>{{ trans('front.altitude') }}</th>
                    <th>{{ trans('front.angle') }}</th>
                    <th>{{ trans('front.duration') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                    <th>{{ trans('front.odometer') }}</th>
                    <th>{{ trans('front.engine_hours') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <tr>
                    @foreach($item['meta'] as $key => $meta)
                        <td>{{ $meta['value'] }}</td>
                    @endforeach
                    @if (isset($item['error']))
                            <td colspan="9">{{ $item['error'] }}</td>
                    @else
                        <td>{{ $item['data']['time'] }}</td>
                        <td>{!! $item['data']['location'] !!}</td>
                        <td>{{ $item['data']['speed'] }}</td>
                        <td>{{ $item['data']['altitude'] }}</td>
                        <td>{{ $item['data']['course'] }}</td>
                        <td>{{ $item['data']['offline_duration'] }}</td>
                        <td>{{ $item['data']['distance'] }}</td>
                        <td>{{ $item['data']['odometer'] }}</td>
                        <td>{{ $item['data']['engine_hours'] }}</td>
                    @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop