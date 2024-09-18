@extends('Admin.Billing.Gateways.layout')

@section('form-fields')
    <div id="braintree">
        <div class="form-group">
            {!! Form::label('merchantId', trans('validation.attributes.merchant_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('merchantId', settings('payments.braintree.merchantId'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('publicKey', trans('validation.attributes.public_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('publicKey', settings('payments.braintree.publicKey'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('privateKey', trans('validation.attributes.private_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('privateKey', settings('payments.braintree.privateKey'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('merchant_account_id', trans('validation.attributes.merchant_account_id'),
            ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('merchant_account_id', settings('payments.braintree.merchant_account_id'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('environment', trans('validation.attributes.environment'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::select('environment', config('payments.braintree.environments'), settings('payments.braintree.environment'), ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label(null, null, ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::hidden('3d_secure', 0) !!}
                <div class="checkbox">
                    {!! Form::checkbox('3d_secure', 1, settings('payments.braintree.3d_secure')) !!}
                    {!! Form::label(null, '3D secure') !!}
                </div>
            </div>
        </div>

        <p><b>{{ trans('validation.attributes.braintree_plan_ids') }}</b></p>
        @if(empty($plans))
            <p>{{ trans('front.plan_not_found') }}</p>
        @else
            <?php $i = 0; ?>
            @foreach($plans as $plan_id => $plan)
                {!! Form::hidden("billing_plans[$i]", $plan_id) !!}
                <div class="form-group plan_group">
                    <div class="col-xs-12 col-sm-4">
                        <p><b>{{ trans('front.plan') }}</b>: {{ ucfirst($plan['title']) }}</p>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        {!! Form::select("plan_ids[$i]", $braintree_plan_ids, $plan['braintree_id'], ['class' => 'form-control']) !!}
                    </div>
                </div>
                <?php $i++ ?>
            @endforeach
        @endif

    </div>
@overwrite