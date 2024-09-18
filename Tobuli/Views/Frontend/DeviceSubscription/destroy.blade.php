@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.cancel') }}
@stop

@section('body')
    {!!Form::open(['route' => ['devices.subscriptions.delete', $subscription->id], 'method' => 'DELETE'])!!}
    {!!Form::hidden('id', $subscription->id)!!}

    <div class="form-group">
        {!! trans('front.do_subscription_delete') !!}
    </div>

    {!!Form::close()!!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop