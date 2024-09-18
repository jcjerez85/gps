@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!!Form::open(['route' => ['device_camera.destroy', $item->id], 'method' => 'DELETE'])!!}
        {!!trans('front.do_delete')!!}
    {!!Form::close()!!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop
