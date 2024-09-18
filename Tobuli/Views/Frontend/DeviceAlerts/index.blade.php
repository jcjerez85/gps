@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon alerts text-primary"></i> {{ trans('front.device_alerts') }} {{ $device->name }}
@stop

@section('body')
    <div id="device_alerts_table">
        <div data-table>
            @include('Frontend.DeviceAlerts.table')
        </div>
    </div>

    <script>
        tables.set_config('device_alerts_table', {
            url: '{!! route('device.alerts.table', ['device_id' => $device->id]) !!}'
        });

        function device_alerts_time_period_edit_modal_callback() {
            tables.get('device_alerts_table');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop

