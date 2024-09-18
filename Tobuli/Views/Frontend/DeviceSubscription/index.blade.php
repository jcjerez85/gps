@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('admin.device_plans') }}
@stop

@section('body')
    <div id="table_device_subscription">
        <div class="panel-body" data-table>
            @include('Frontend.DeviceSubscription.table')
        </div>
    </div>

    <script>
        tables.set_config('table_device_subscription', {
            url:'{{ route("devices.subscriptions.table") }}',
        });

        function device_subscription_cancel_modal_callback() {
            tables.get('table_device_subscription');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
