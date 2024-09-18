@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else

                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('front.start') }}</th>
                                <th>{{ trans('front.end') }}</th>
                                <th>{{ trans('front.allowed_speed_limit') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('front.top_speed') }}</th>
                                <th>{{ trans('front.average_speed') }}</th>
                                <th>{{ trans('front.position') }}</th>
                            </tr>

                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['end_at'] }}</td>
                                    <td>{{ $row['overspeed_limit'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['speed_max'] }}</td>
                                    <td>{{ $row['speed_avg'] }}</td>

                                    <td>{!! $row['location'] !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop