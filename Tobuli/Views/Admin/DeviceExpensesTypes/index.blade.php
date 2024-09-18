@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_device_expenses_types">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:"
                       type="button"
                       data-modal="device_expenses_types_create"
                       data-url="{{ route("admin.device_expenses_types.create") }}">
                        <i class="icon add" title="{{ trans('global.add') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">
                <i class="icon user"></i>
                {!! trans('admin.expenses_types') !!}
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.DeviceExpensesTypes.table')
        </div>
    </div>
@stop

@section('javascript')
    <script>
        tables.set_config('table_device_expenses_types', {
            url:'{{ route("admin.device_expenses_types.index") }}',
            delete_url:'{{ route("admin.device_expenses_types.destroy") }}'
        });

        function device_expenses_types_edit_modal_callback() {
            tables.get('table_device_expenses_types');
        }

        function device_expenses_types_create_modal_callback() {
            tables.get('table_device_expenses_types');
        }
    </script>
@stop