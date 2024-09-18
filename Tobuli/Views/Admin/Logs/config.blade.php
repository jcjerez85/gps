@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon logs"></i> {{ trans('admin.logs') }}
@stop

@section('body')
    {!! Form::open(array('url' => route('admin.logs.config.set'), 'method' => 'POST')) !!}
    @foreach ($levels as $level => $levelName)
    <div class="form-group">
        <div class="radio">
            {!! Form::radio('level', $level, $level == $current) !!}
            {!! Form::label(null, $levelName) !!}
        </div>
    </div>
    @endforeach
    {!! Form::close() !!}
@stop