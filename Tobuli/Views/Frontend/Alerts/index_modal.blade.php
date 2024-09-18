@extends('front::Layouts.modal')
@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon alerts"></i> {!!trans('front.alerts')!!}
@stop

@section('body')
    <div class="row" id="table_alerts">
        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="pull-right">
            <ul class="nav nav-tabs nav-icons">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="alerts_create" data-url="{{ route('alerts.create') }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-xs-4">
            <div class="form-group search">
                {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
            </div>
        </div>

        <div class="col-xs-12">
            <div data-table>
                @include('front::Alerts.table')
            </div>
        </div>
    </div>
@stop

<script>
    tables.set_config('table_alerts', {
        url: '{{ route("alerts.table") }}',
        destroy: '{{ route("alerts.destroy") }}',
        set_active: {
            url: '{{ route("alerts.change_active", 1) }}',
            method: 'POST'
        },
        set_inactive: {
            url: '{{ route("alerts.change_active", 0) }}',
            method: 'POST'
        }
    });

    function alerts_edit_modal_callback() {
        tables.get('table_alerts');
    }

    function alerts_create_modal_callback() {
        tables.get('table_alerts');
    }

    function alerts_destroy_modal_callback() {
        tables.get('table_alerts');
    }
</script>

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!! trans('global.cancel') !!}</button>
@stop