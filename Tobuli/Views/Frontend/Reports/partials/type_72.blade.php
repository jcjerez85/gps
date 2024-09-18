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
                        <th>{{ trans('front.driver') }}</th>
                        <th>{{ trans('front.start_date') }}</th>
                        <th>{{ trans('front.start_time') }}</th>
                        <th>{{ trans('front.stop_date') }}</th>
                        <th>{{ trans('front.stop_time') }}</th>
                        <th>{{ trans('global.distance') }}</th>
                        <th>{{ trans('front.duration') }}</th>
                        <th>{{ trans('front.stop_duration') }}</th>
                        <th>{{ trans('front.move_duration') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="9">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach ($item['table']['rows'] as $row)
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td>{{ $row['drivers'] }}</td>
                            <td>{{ $row['start_date'] }}</td>
                            <td>{{ $row['start_time'] }}</td>
                            <td>{{ $row['stop_date'] }}</td>
                            <td>{{ $row['stop_time'] }}</td>
                            <td>{{ $row['distance'] }}</td>
                            <td>{{ $row['duration'] }}</td>
                            <td>{{ $row['stop_duration'] }}</td>
                            <td>{{ $row['drive_duration'] }}</td>
                        </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop