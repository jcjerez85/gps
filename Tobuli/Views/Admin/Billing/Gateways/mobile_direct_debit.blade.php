@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div class="form-group payment-mobile_direct_debit">
        {!! Form::label('url', 'URL', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('url', settings('payments.mobile_direct_debit.url'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-mobile_direct_debit">
        {!! Form::label('api_key', 'API key', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('api_key', settings('payments.mobile_direct_debit.api_key'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-mobile_direct_debit">
        {!! Form::label('merchant_id', 'Merchant ID', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('merchant_id', settings('payments.mobile_direct_debit.merchant_id'), ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group payment-mobile_direct_debit">
        {!! Form::label('product_id', 'Product ID', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
        <div class="col-xs-12 col-sm-8">
            {!! Form::text('product_id', settings('payments.mobile_direct_debit.product_id'), ['class' => 'form-control']) !!}
        </div>
    </div>
@overwrite