@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else
                @if( ! empty($item['totals']))
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td class="col-sm-6">
                                    @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['start', 'end', 'distance', 'drive_duration', 'stop_duration', 'speed_max', 'speed_avg', 'overspeed_count', 'underspeed_count']])
                                </td>
                                <td class="col-sm-6">
                                    @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['fuel_consumption_list', 'fuel_price_list', 'engine_hours', 'engine_work', 'engine_idle', 'odometer', 'drivers']])
                                </td>
                            </tr>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop