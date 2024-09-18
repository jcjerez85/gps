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
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('front.engine_idle') }}</th>
                                <th>{{ trans('front.driver') }}</th>
                                <th>{{ trans('front.stop_position') }}</th>
                                @if ($report->zones_instead)
                                    <th>{{ trans('front.geofences') }}</th>
                                @endif
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['end_at'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['engine_idle'] }}</td>
                                    <td>{{ $row['drivers'] }}</td>
                                    <td>{!! $row['location'] !!}</td>

                                    @if ($report->zones_instead)
                                        <td>{{ \Illuminate\Support\Arr::get($row, 'geofences_in') }}</td>
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