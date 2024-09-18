@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_device_types">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="device_types_create" data-url="{{ route("admin.device_type.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{!! trans('admin.device_types') !!}</div>
        </div>

        <div class="panel-body">
            <div data-table>
                @include('Admin.DeviceTypes.table')
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_device_types', {
        url:'{{ route("admin.device_type.index") }}'
    });

    function device_types_edit_modal_callback() {
        tables.get('table_device_types');
    }

    function device_types_create_modal_callback() {
        tables.get('table_device_types');
    }
</script>
@stop