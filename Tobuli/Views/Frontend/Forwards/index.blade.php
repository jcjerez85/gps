@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon forwards"></i> {{ trans('front.forwards') }}
@stop

@section('body')
    <div class="action-block">
        <a href="javascript:" class="btn btn-action" data-url="{!!route('forwards.create')!!}" data-modal="forwards_create" type="button">
            <i class="icon add"></i> {{ trans('global.add') }}
        </a>
    </div>

    <div id="forwards_table">
        <div data-table>
            @include('Frontend.Forwards.table')
        </div>
    </div>

    <script>
        tables.set_config('forwards_table', {
            url:'{{ route('forwards.table') }}',
            destroy: '{{ route('forwards.destroy') }}',
            _models: new Array('forwards')
        });
        function forwards_create_modal_callback() {
            tables.get('forwards_table');
        }
        function forwards_edit_modal_callback() {
            tables.get('forwards_table');
        }
        function forwards_destroy_modal_callback() {
            tables.get('forwards_table');
        }
    </script>
@stop

@section('buttons')

@stop