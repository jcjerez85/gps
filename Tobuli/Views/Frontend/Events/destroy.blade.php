@extends('Frontend.Layouts.modal')

@section('title', trans('global.delete'))

@section('body')
    {!!Form::open(['route' => 'events.destroy', 'method' => 'DELETE'])!!}
        @if ($id)
            {!! Form::hidden('id', $id) !!}
            {!!trans('front.do_delete')!!}
        @else
            {!!trans('front.do_delete_events')!!}
        @endif
    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.yes')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.no')!!}</button>
@stop