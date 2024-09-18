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
                                @foreach($report->metas('device') as $meta)
                                    <th rowspan="2">{{ $meta['title'] }}</th>
                                @endforeach
                                <th rowspan="2">{{ trans('validation.attributes.status') }}</th>
                                <th rowspan="2">{{ trans('front.start') }}</th>
                                <th rowspan="2">{{ trans('front.end') }}</th>
                                <th rowspan="2">{{ trans('front.duration') }}</th>
                                <th colspan="1">{{ trans('front.stop_position') }}</th>
                                <th rowspan="2">{{ trans('front.fuel_consumption') }}</th>
                                @if ($report->zones_instead)
                                    <th rowspan="2">{{ trans('front.geofences') }}</th>
                                @endif
                            </tr>
                            <tr align="center">
                                <th>{{ trans('front.route_length') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    @foreach($report->metas('device') as $key => $meta)
                                        <td>{{ $row[$key] }}</td>
                                    @endforeach
                                    <td>{{ $row['status'] }}</td>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['end_at'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    @if ($row['group_key'] == 'drive')
                                        <td>{{ $row['distance'] }}</td>
                                    @else
                                        <td>{!! $row['location'] !!}</td>
                                    @endif
                                        <td>{{ $row['fuel_consumption'] }}</td>
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