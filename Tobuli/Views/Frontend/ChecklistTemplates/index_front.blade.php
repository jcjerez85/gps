@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('admin.checklist_templates') }}
@stop

@section('body')
    <div id="table_checklist_template">
        <div class="action-block">
            <a href="javascript:"
               class="btn btn-action"
               type="button"
               data-modal="checklist_template_create"
               data-url="{{ route("checklist_template.create") }}">
                <i class="icon add" title="{{ trans('global.add') }}"></i> {{ trans('global.add') }}
            </a>
        </div>

        <div class="panel-body" data-table>
            @include('Frontend.ChecklistTemplates.table')
        </div>
    </div>

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

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
