@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div class="form-group">
        {!! Form::label('project_id', trans('validation.attributes.project_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('project_id', settings('payments.paysera.project_id'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('project_psw', trans('validation.attributes.project_psw'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('project_psw', settings('payments.paysera.project_psw'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('verify_id', trans('validation.attributes.verify_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('verify_id', settings('payments.paysera.verify_id'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('currency', trans('validation.attributes.currency'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('currency', settings('payments.paysera.currency'), ['class' => 'form-control', 'placeholder' => 'EUR']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('environment', trans('validation.attributes.environment'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::select('environment', config('payments.paysera.environments'), settings('payments.paysera.environment'), ['class' => 'form-control']) !!}
        </div>
    </div>
@overwrite