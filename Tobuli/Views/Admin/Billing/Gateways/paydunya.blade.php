@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div class="form-group">
        {!! Form::label('master_key', trans('validation.attributes.master_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('master_key', settings('payments.paydunya.master_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('public_key', trans('validation.attributes.public_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('public_key', settings('payments.paydunya.public_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('private_key', trans('validation.attributes.private_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('private_key', settings('payments.paydunya.private_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('token', trans('validation.attributes.token'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('token', settings('payments.paydunya.token'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('token', trans('validation.attributes.payment_name'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('payment_name', settings('payments.paydunya.payment_name'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label('mode', trans('validation.attributes.environment'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::select('mode', config('payments.paydunya.environments'), settings('payments.paydunya.mode'), ['class' => 'form-control']) !!}
        </div>
    </div>
@overwrite