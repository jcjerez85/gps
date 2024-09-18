@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_media_categories">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="media_categories_create" data-url="{{ route("admin.media_category.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{!! trans('front.media_categories') !!}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div data-table>
                @include('Admin.MediaCategories.table')
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_media_categories', {
        url:'{{ route("admin.media_category.index") }}'
    });

    function media_categories_edit_modal_callback() {
        tables.get('table_media_categories');
    }

    function media_categories_create_modal_callback() {
        tables.get('table_media_categories');
    }
</script>
@stop