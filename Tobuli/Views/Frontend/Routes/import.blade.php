@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.import') }}
@stop

@section('body')
    {!! Form::open(['route' => 'routes.import', 'method' => 'POST']) !!}

    <div class="form-group">
        {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
        {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action" onclick="app.routes.import( this );">{{ trans('front.import') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop