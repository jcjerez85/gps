@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        @if ( ! empty($item['table']))
            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('validation.attributes.date') }}</th>
                        <th>{{ trans('front.travel_start_time') }}</th>
                        <th>{{ trans('front.travel_end_time') }}</th>
                        <th>{{ trans('front.travel_time') }}</th>
                        <th>{{ trans('front.distance_travelled') }}</th>
                        <th>{{ trans('front.move_duration') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($item['table']['rows'] as $row)
                        <tr>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['first_drive_time'] }}</td>
                            <td>{{ $row['last_drive_time'] }}</td>
                            <td>{{ $row['duration'] }}</td>
                            <td>{{ $row['distance'] }}</td>
                            <td>{{ $row['drive_duration'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endforeach
@stop