@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div id="kevin">
        <div class="form-group">
            {!! Form::label('client_id', 'Client ID', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('client_id', settings('payments.kevin.client_id'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('client_secret', 'Client secret', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('client_secret', settings('payments.kevin.client_secret'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('endpoint_secret', 'Endpoint secret', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('endpoint_secret', settings('payments.kevin.endpoint_secret'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('currency', 'Currency', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('currency', settings('payments.kevin.currency'), ['class' => 'form-control', 'placeholder' => 'EUR']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('language', 'Language', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('language', settings('payments.kevin.language'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('receiver_name', 'Receiver name', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('receiver_name', settings('payments.kevin.receiver_name'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('receiver_iban', 'Receiver IBAN', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}

            <div class="col-xs-12 col-sm-8">
                {!! Form::text('receiver_iban', settings('payments.kevin.receiver_iban'), ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
@overwrite