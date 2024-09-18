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
                                <th>{{ trans('front.zone_in') }}</th>
                                <th>{{ trans('front.zone_out') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('validation.attributes.geofence_name') }}</th>
                                <th>{{ trans('front.ignition_on_off') }}</th>
                                <th>{{ trans('front.position') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['end_at'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['group_geofence'] }}</td>
                                    <td>{{ $row['status'] }}</td>
                                    <td>{!! $row['location'] !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            @foreach ($item['table']['totals'] as $row)
                            <tr>
                                <td></td>
                                <td>{{ $row['geofence_name'] }}</td>
                                <td>{{ $row['engine_on_duration'] }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endforeach
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop