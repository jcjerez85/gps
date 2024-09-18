@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div id="twocheckout">
        <div class="form-group">
            {!! Form::label('api_url', 'API URL', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('api_url', settings('payments.twocheckout.api_url'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('front_url', 'URL', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('front_url', settings('payments.twocheckout.front_url'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('merchant_code', trans('validation.attributes.merchant_code'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('merchant_code', settings('payments.twocheckout.merchant_code'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('secret_key', trans('validation.attributes.secret_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('secret_key', settings('payments.twocheckout.secret_key'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label(null, null, ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::hidden('demo_mode', 0) !!}
                <div class="checkbox">
                    {!! Form::checkbox('demo_mode', 1, settings('payments.twocheckout.demo_mode')) !!}
                    {!! Form::label('demo_mode', 'Demo mode') !!}
                </div>
            </div>
        </div>
    </div>
@overwrite