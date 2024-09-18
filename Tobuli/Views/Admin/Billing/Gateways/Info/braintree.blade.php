@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <p><b>{{ trans('validation.attributes.braintree_plan_ids') }}</b><br>{{ trans('validation.attributes.braintree_plan_explanation') }}</p>
    <p><b>{{ trans('validation.attributes.merchant_id') }}</b><br>{{ trans('validation.attributes.braintree_merchant_explanation') }}</p>
    <p><b>{{ trans('validation.attributes.braintree_currency_match') }}</b></p>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop