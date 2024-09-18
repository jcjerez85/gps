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
                    <th>{{ trans('front.route_start') }}</th>
                    <th>{{ trans('front.route_end') }}</th>
                    <th>{{ trans('front.route_length') }}</th>
                    <th>{{ trans('front.move_duration') }}</th>
                    <th>{{ trans('front.stop_duration') }}</th>
                    <th>{{ trans('front.top_speed') }}</th>
                    <th>{{ trans('front.average_speed') }}</th>
                    <th>{{ trans('front.overspeed_count') }}</th>
                    <th>{{ trans('front.fuel_consumption') }}</th>
                    <th>{{ trans('front.fuel_cost') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <tr>
                    @foreach($item['meta'] as $key => $meta)
                        <td>{{ $meta['value'] }}</td>
                    @endforeach
                    @if (isset($item['error']))
                            <td colspan="10">{{ $item['error'] }}</td>
                    @else
                        <td>{{ $item['totals']['start_at'] }}</td>
                        <td>{{ $item['totals']['end_at'] }}</td>
                        <td>{{ $item['totals']['distance'] }}</td>
                        <td>{{ $item['totals']['drive_duration'] }}</td>
                        <td>{{ $item['totals']['stop_duration'] }}</td>
                        <td>{{ $item['totals']['speed_max'] }}</td>
                        <td>{{ $item['totals']['speed_avg'] }}</td>
                        <td>{{ $item['totals']['overspeed_count'] }}</td>
                        <td>{{ $item['totals']['fuel_consumption'] }}</td>
                        <td>{{ $item['totals']['fuel_price'] }}</td>
                    @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop