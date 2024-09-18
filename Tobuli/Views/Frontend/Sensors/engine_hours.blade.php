@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon engine_hours"></i> {{ trans('front.engine_hours') }}
@stop

@section('body')
    {!! Form::open(['route' => 'sensors.set_engine_hours', 'method' => 'POST']) !!}
        {!! Form::hidden('device_id', $device_id) !!}
        <div class="form-group">
            {!! Form::text('engine_hours', $engine_hours, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
    <script>
        $(document).ready(function() {});
    </script>
@stop