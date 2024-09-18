@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div class="form-group payment-stripe">
        {!! Form::label('public_key', trans('validation.attributes.public_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('public_key', settings('payments.stripe.public_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label('secret_key', trans('validation.attributes.secret_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('secret_key', settings('payments.stripe.secret_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label('currency', trans('validation.attributes.currency'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('currency', settings('payments.stripe.currency'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label(null, null, ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::hidden('one_time_payment', 0) !!}
            <div class="checkbox">
                {!! Form::checkbox('one_time_payment', 1, settings('payments.stripe.one_time_payment')) !!}
                {!! Form::label(null, trans('validation.attributes.one_time_payment')) !!}
            </div>
        </div>
    </div>

    <div class="form-group payment-stripe">
        {!! Form::label('webhook_key', trans('validation.attributes.webhook_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('webhook_key', settings('payments.stripe.webhook_key'), ['class' => 'form-control']) !!}
        </div>
    </div>
@overwrite
