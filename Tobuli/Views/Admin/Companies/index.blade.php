@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_companies">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="companies_create" data-url="{{ route("admin.companies.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{{ trans('admin.companies') }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.Companies.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_companies', {
        url:'{{ route("admin.companies.index") }}',
        delete_url:'{{ route("admin.companies.destroy") }}'
    });

    function companies_edit_modal_callback() {
        tables.get('table_companies');
    }

    function companies_create_modal_callback() {
        tables.get('table_companies');
    }
</script>
@stop