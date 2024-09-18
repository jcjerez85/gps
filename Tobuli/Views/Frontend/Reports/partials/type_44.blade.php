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
                    <th>{{ trans('front.move_duration') }}</th>
                    <th>{{ trans('front.stop_duration') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $date => $items)
                    @if ( ! isset($item['error']))
                        <tr>
                            <td><strong>{{ $date }}</strong></td>
                            <td colspan="{{ 4 + count($report->metas('device')) - 1 }}"></td>
                        </tr>
                        @foreach($items as $item)
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    @foreach($item['meta'] as $key => $meta)
                                        <td>{{ $meta['value'] }}</td>
                                    @endforeach
                                    <td>{{ $row['geofences'] }}</td>
                                    <td>{{ $row['drive_duration'] }}</td>
                                    <td>{{ $row['stop_duration'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    @foreach($report->metas('device') as $meta)
                        <td></td>
                    @endforeach
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $report->globalTotals('distance') }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop