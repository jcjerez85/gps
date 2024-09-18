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
                                <th>{{ trans('validation.attributes.geofence_name') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('global.distance') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['group_geofence'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td>{{ $item['table']['totals']['duration'] }}</td>
                                <td>{{ $item['table']['totals']['distance'] }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif

                @include('Frontend.Reports.partials.item_total')
            @endif
        </div>
    @endforeach
@stop