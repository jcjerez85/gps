@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon custom-field"></i> {{ trans('admin.custom_fields') }}
@stop

@section('body')
    {!! Form::open(['route' => ['admin.custom_fields.store'], 'method' => 'POST']) !!}
        <div class="custom-fields-form">
            @include('Admin.CustomFields.form')
        </div>
    {!! Form::close() !!}
@stop
