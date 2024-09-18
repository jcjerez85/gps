@extends('Admin.Layouts.default')

@section('content')
<div class="panel panel-default" id="table_checklist_template">

    <div class="panel-heading">
        <ul class="nav nav-tabs nav-icons pull-right">
            <li role="presentation" class="">
                <a href="javascript:" type="button" data-modal="checklist_template_create" data-url="{{ route("checklist_template.create") }}">
                    <i class="icon add" title="{{ trans('admin.create') }}"></i>
                </a>
            </li>
        </ul>

        <div class="panel-title">{{ trans('admin.checklist_templates') }}</div>
    </div>

    <div class="panel-body" data-table>
        @include('Frontend.ChecklistTemplates.table')
    </div>
</div>
@stop

@section('javascript')
<script>
    tables.set_config('table_checklist_template', {
        url:'{{ route("checklist_template.table") }}',
    });

    function checklist_template_edit_modal_callback() {
        tables.get('table_checklist_template');
    }

    function checklist_template_create_modal_callback() {
        tables.get('table_checklist_template');
    }

    function checklist_template_delete_modal_callback() {
        tables.get('table_checklist_template');
    }
</script>
@stop