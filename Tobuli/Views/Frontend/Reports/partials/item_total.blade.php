@if( ! empty($item['totals']))
<div class="panel-body">
    <table class="table">
        <tr>
            <td class="col-sm-6">
                @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['distance', 'drive_duration', 'stop_duration', 'speed_max', 'speed_avg', 'overspeed_count', 'underspeed_count', 'harsh_acceleration_count', 'harsh_breaking_count']])
            </td>
            <td class="col-sm-6">
                @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['fuel_consumption_list', 'fuel_price_list', 'engine_hours', 'engine_work', 'engine_idle', 'odometer', 'odometer_diff_list', 'odometer_start', 'odometer_end', 'drivers']])
            </td>
        </tr>
    </table>
</div>
@endif