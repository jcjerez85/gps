@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon device"></i>
@stop

@section('body')
    {!! Form::open(['route' => ['sharing_device.save_to_sharing', 'device_id' => $deviceId], 'method' => 'POST']) !!}
        <div class="form-group">
            {!! Form::label('sharing_id', trans('validation.attributes.sharing_id').':') !!}
            {!! Form::select('sharing_id', $sharings, null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop
