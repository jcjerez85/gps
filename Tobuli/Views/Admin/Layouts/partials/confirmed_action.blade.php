@extends('Frontend.Layouts.modal')

@section('title')
    {{ $title }}
@stop

@section('body')
    {!! Form::open(['url' => $url, 'method' => $method]) !!}

    {!! arrayToHtmlInput($input) !!}

    <div class="form-group">
        {!! $description !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop