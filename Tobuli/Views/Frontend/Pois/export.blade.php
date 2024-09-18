@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.export') }}
@stop

@section('body')
    {!! Form::open(['route' => 'pois.export', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}
    <div class="form-group">
        {!! Form::label('export_format', trans('validation.attributes.format').':') !!}
        {!! Form::select('export_format', $exportFormats, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('export_type', trans('validation.attributes.export_type').':') !!}
        {!! Form::select('export_type', $exportTypes, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group pois-export-input">
        {!! Form::label('pois', trans('validation.attributes.pois').':') !!}
        {!! Form::select('pois[]', $pois, null, ['class' => 'form-control', 'multiple' => 'multiple']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action" onclick="$('#pois_export form').submit();" data-dismiss="modal">{{ trans('front.export') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop