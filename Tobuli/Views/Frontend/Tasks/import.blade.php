@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon task-import"></i> {{ trans('front.import') }}
@stop

@section('body')
    <div class="alert alert-info small">
        <a href="{{ asset('examples/import_task.csv') }}">example.csv</a>,
        <a href="{{ asset('examples/import_task.xls') }}">example.xls</a>
    </div>

    {!! Form::open(['route' => 'tasks.import_set', 'method' => 'POST', 'files' => true]) !!}
        <div class="form-group">
            {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
            {!! Form::file('file', [
                'class' => 'form-control',
                'accept' => '.xlsx, .xls, .csv',
                'onChange' => 'getImportFields("use_map", "file", "task", "fields-map")'
            ]) !!}
        </div>

        <div class="form-group">
            <div class="checkbox-inline">
                {!! Form::checkbox('use_map', 1, null, [
                    'id' => 'use_map',
                    'onChange' => 'getImportFields("use_map", "file", "task", "fields-map")'
                ]) !!}
                {!! Form::label('use_map', trans('front.table_map')) !!}
            </div>
        </div>

        <div id="fields-map" class="form-group"></div>
    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop
