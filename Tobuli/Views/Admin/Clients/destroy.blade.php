@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.clients.destroy', 'method' => 'DELETE']) !!}

    @foreach($ids as $id)
        {!! Form::hidden('ids[]', $id) !!}
    @endforeach

    <div class="form-group">
        {!! trans('admin.do_delete') !!}
    </div>

    {!! Form::close() !!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop