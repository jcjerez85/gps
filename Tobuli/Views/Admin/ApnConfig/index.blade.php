@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_apn_config">
        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <div class="panel-title"><i class="icon satellites"></i> {!! trans('front.apn_configuration') !!}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>

            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="apn_config_create" data-url="{{ route('admin.apn_config.create') }}">
                        <i class="icon add" title="{{ trans('global.add') }}"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.ApnConfig.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_apn_config', {
        url:'{{ route("admin.apn_config.index") }}',
    });

    function apn_config_edit_modal_callback() {
        tables.get('table_apn_config');
    }

    function apn_config_create_modal_callback() {
        tables.get('table_apn_config');
    }
</script>
@stop
