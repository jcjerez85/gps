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
                    <th>{{ trans('front.geofences') }}</th>
                    <th>{{ trans('front.start') }}</th>
                    <th>{{ trans('front.end') }}</th>
                    <th>{{ trans('front.duration') }}</th>
                    <th>{{ trans('front.top_speed') }}</th>
                    <th>{{ trans('front.average_speed') }}</th>
                    <th>{{ trans('front.position') }}</th>
                    <th>{{ trans('front.driver') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="8">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach ($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['geofences_in'] }}</td>
                                <td>{{ $row['start_at'] }}</td>
                                <td>{{ $row['end_at'] }}</td>
                                <td>{{ $row['duration'] }}</td>
                                <td>{{ $row['speed_max'] }}</td>
                                <td>{{ $row['speed_avg'] }}</td>
                                <td>{!! $row['location'] !!}</td>
                                <td>{{ $row['drivers'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    @foreach($report->metas('device') as $meta)
                        <th></th>
                    @endforeach
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $report->globalTotals('duration') }}</th>
                    <th>{{ $report->globalTotals('speed_max') }}</th>
                    <th>{{ $report->globalTotals('speed_avg') }}</th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop