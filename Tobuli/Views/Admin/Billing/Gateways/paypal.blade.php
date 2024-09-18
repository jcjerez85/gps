@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div class="form-group payment-paypal">
        {!! Form::label('client_id', trans('validation.attributes.client_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('client_id', settings('payments.paypal.client_id'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-paypal">
        {!! Form::label('secret', trans('validation.attributes.secret'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('secret', settings('payments.paypal.secret'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-paypal">
        {!! Form::label('payment_name', trans('validation.attributes.payment_name'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('payment_name', settings('payments.paypal.payment_name'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-paypal">
        {!! Form::label('currency', trans('validation.attributes.currency'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('currency', settings('payments.paypal.currency'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label('mode', trans('validation.attributes.environment'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::select('mode', config('payments.paypal.environments'), settings('payments.paypal.mode'), ['class' => 'form-control']) !!}
        </div>
    </div>
@overwrite