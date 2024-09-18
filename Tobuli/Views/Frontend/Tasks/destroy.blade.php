@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!! Form::open(['route' => 'tasks.destroy', 'method' => 'DELETE']) !!}
    @foreach($ids as $index => $id)
        {{ Form::hidden("id[$index]", $id) }}
    @endforeach
    {!! trans('front.do_task_delete') !!}
    {!! Form::close() !!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{{ trans('global.yes') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.no') }}</button>
@stop