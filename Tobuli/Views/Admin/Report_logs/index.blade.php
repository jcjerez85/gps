@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_{{ $section }}">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon logs"></i> {{ trans('admin.'.$section) }}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
                <div class="form-group">
                    {!! Form::select('is_send[]', ['1' => 'Yes', '0' => 'No'], null, ['class' => 'form-control', 'multiple' => 'multiple', 'title' => trans('global.is_send'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    tables.set_config('table_{{ $section }}', {
        url:'{{ route("admin.{$section}.index") }}',
        prompt_delete_url:'{{ route("admin.{$section}.destroy") }}',
        prompt_delete_all_url: '{{ route("admin.{$section}.destroy", ['all' => 1]) }}',
        _models: new Array('report_logs')
    });
</script>
@stop