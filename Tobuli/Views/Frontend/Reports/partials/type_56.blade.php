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
                    <th>{{ trans('front.route_length') }}</th>
                    <th>{{ trans('front.stop_count') }}</th>
                    <th>{{ trans('front.route_start') }}</th>
                    <th>{{ trans('front.route_end') }}</th>
                    <th>{{ trans('front.engine_work') }}</th>
                    <th>{{ trans('front.idle_duration') }}</th>
                    <th>{{ trans('front.stop_duration') }}</th>
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
                            <td colspan="8">{{ $item['error'] }}</td>
                    @else
                        <td>{{ $item['totals']['distance'] }}</td>
                        <td>{{ $item['totals']['stop_count'] }}</td>
                        <td>{{ $item['totals']['start_at'] }}</td>
                        <td>{{ $item['totals']['end_at'] }}</td>
                        <td>{{ $item['totals']['engine_work'] }}</td>
                        <td>{{ $item['totals']['engine_idle'] }}</td>
                        <td>{{ $item['totals']['stop_duration'] }}</td>
                        <td>{{ $item['totals']['engine_hours'] }}</td>
                    @endif
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        @foreach($report->metas() as $meta)
                            <td></td>
                        @endforeach
                        <td>{{ $report->globalTotals('distance') }}</td>
                        <td>{{ $report->globalTotals('stop_count') }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $report->globalTotals('engine_work') }}</td>
                        <td>{{ $report->globalTotals('engine_idle') }}</td>
                        <td>{{ $report->globalTotals('stop_duration') }}</td>
                        <td>{{ $report->globalTotals('engine_hours') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop