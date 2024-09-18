@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.apn_config.store', 'method' => 'POST']) !!}
        <div class="checkbox">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, true) !!}
            {!! Form::label('active', trans('validation.attributes.active') ) !!}
        </div>
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name')) !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_name', trans('validation.attributes.apn_name')) !!}
            {!! Form::text('apn_name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_username', trans('validation.attributes.username')) !!}
            {!! Form::text('apn_username', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_password', trans('validation.attributes.password')) !!}
            {!! Form::text('apn_password', null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop
