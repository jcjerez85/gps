@extends('Admin.Layouts.modal')

@section('title')
    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.objects.bulk_delete', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}

    <div class="alert alert-success" role="alert" style="display: none;"></div>

    <div class="form-group">
        {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
        {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.delete') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop