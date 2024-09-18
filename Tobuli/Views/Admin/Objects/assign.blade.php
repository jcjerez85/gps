@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.objects.assign', 'method' => 'POST'])!!}
    @foreach($device_id as $id)
        {!!Form::hidden('device_id[]', $id)!!}
    @endforeach

    <div class="form-group">
        <div class="radio-inline">
            {!! Form::radio('action', 'attach', 1) !!}
            {!! Form::label('action', trans('front.attach') ) !!}
        </div>
        <div class="radio-inline">
            {!! Form::radio('action', 'detach', 0) !!}
            {!! Form::label('action', trans('front.detach') ) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('user_id', trans('validation.attributes.user').'*:') !!}
        {!! Form::select('user_id[]', $users->pluck('email', 'id'), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => 'true']) !!}
    </div>

    {!!Form::close()!!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop