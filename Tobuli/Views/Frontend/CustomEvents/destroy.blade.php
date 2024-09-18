@extends('Frontend.Layouts.modal')

@section('title', trans('global.delete'))

@section('body')
    {!!Form::open(['route' => 'custom_events.destroy', 'method' => 'DELETE'])!!}
        {!!Form::hidden('id', $item->id)!!}
        {!!trans('front.do_delete')!!}
    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.yes')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.no')!!}</button>
@stop