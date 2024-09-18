@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="forwards_table">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="forwards_create" data-url="{{ route("admin.forwards.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title"><i class="icon forwards"></i> {{ trans('admin.forwards') }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.Forwards.table')
        </div>
    </div>
@stop

@section('javascript')
    <script>
        tables.set_config('forwards_table', {
            url:'{{ route('admin.forwards.table') }}',
            destroy: '{{ route('admin.forwards.destroy') }}',
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