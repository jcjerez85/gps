@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_device_type_imei">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="device_type_imei_import" data-url="{{ route("admin.device_type_imei.importForm") }}">
                        <i class="icon import" title="{{ trans('admin.import') }}"></i>
                    </a>
                </li>

                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="device_type_imei_create" data-url="{{ route("admin.device_type_imei.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{!! trans('admin.device_type_imei') !!}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => '', 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div data-table>
                @include('Admin.DeviceTypeImei.table')
            </div>
        </div>
    </div>
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

    function device_type_imei_import_modal_callback() {
        tables.get('table_device_type_imei');
    }
</script>
@stop