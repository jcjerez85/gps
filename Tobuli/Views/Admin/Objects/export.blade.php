@extends('Admin.Layouts.modal')

@section('title')
    <i class="fa fa-download"></i> {{ trans('front.export') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.objects.export', 'method' => 'POST', 'id' => 'objects_export']) !!}

    <div class="form-group">
        {!! Form::label('format', trans('validation.attributes.format')) !!}
        {!! Form::select('format', $formats, null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('fields', trans('validation.attributes.fields').'*:') !!}
        {!! Form::select('fields[]', $fields, null, ['class' => 'form-control multiexpand half', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action" onclick="$('#objects_export').submit();"
            data-dismiss="modal">{{ trans('front.export') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop