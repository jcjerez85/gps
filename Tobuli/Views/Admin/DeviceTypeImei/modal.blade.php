@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon"></i> {{ trans('global.imei') }}
@stop

@section('body')
    <div class="action-block">
        <a href="javascript:" class="btn btn-action" data-url="{!! route('admin.device_type_imei.create')!!}"
           data-modal="device_type_imei_create" type="button">
            <i class="icon add"></i> {{ trans('global.add_new') }}
        </a>
    </div>
    <div data-table>
        @include('Admin.DeviceTypeImei.table')
    </div>
@stop

@section('buttons')

@stop

@section('javascript')
<script>
    tables.set_config('table_device_type_imei', {
        url:'{{ route("admin.device_type_imei.table") }}'
    });

    function device_type_imei_edit_modal_callback() {
        tables.get('table_device_type_imei');
    }

    function device_type_imei_create_modal_callback() {
        tables.get('table_device_type_imei');
    }
</script>
@stop