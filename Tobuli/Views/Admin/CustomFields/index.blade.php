@extends('Admin.Layouts.default')

@section('content')
<div class="panel panel-default" id="table_custom_fields">

    <div class="panel-heading">
        <ul class="nav nav-tabs nav-icons pull-right">
            <li role="presentation" class="">
                <a href="javascript:"
                   type="button"
                   data-modal="custom_fields_create"
                   data-url="{{ route("admin.custom_fields.create", $model) }}">
                    <i class="icon add" title="{{ trans('admin.create') }}"></i>
                </a>
            </li>
        </ul>

        <div class="panel-title">{{ trans('admin.custom_fields') }}</div>
    </div>

    <div class="panel-body" data-table>
        @include('Admin.CustomFields.table')
    </div>
</div>
@stop

@section('javascript')
<script>
    tables.set_config('table_custom_fields', {
        url:'{{ route("admin.custom_fields.table", $model) }}',
    });

    function custom_fields_edit_modal_callback() {
        tables.get('table_custom_fields');
    }

    function custom_fields_create_modal_callback() {
        tables.get('table_custom_fields');
    }

    function custom_fields_delete_modal_callback() {
        tables.get('table_custom_fields');
    }
</script>
@stop