@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon device-import"></i> {{ trans('front.import') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.objects.import_set', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}

    <div class="form-group">
        {!! Form::label('file', 'CSV ' . trans('validation.attributes.file').'*:') !!}
        {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}

    <div class="alert alert-info small">
        <a href="{{ asset('examples/import_device.csv') }}">example.csv</a>
    </div>
@stop

@section('footer')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop