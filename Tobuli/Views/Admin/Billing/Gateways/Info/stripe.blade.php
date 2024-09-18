@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <p>
        <b>{{ trans('validation.attributes.currency') }}</b>
        <a href="https://en.wikipedia.org/wiki/ISO_4217">{{ trans('validation.attributes.three_letter_iso') }}</a>
    </p>
    <p>
        <b>{{ trans('validation.attributes.public_key') }}</b>
        Please make sure that public key is correct, because it is not testable from here.
    </p>
    <p>
        <b>{{ trans('validation.attributes.one_time_payment') }}</b>
        One time payment - by checking this tick box you will  make a one attempt to charge the client and should the payment go through the subscription will be created/subscription extended. This type of payment will not create automatic renewals in stripe platform therefore once the time expires, the customer will be required to make this payment again.
    </p>

    <p>
        <b>{{ trans('validation.attributes.webhook_key') }}</b>
        Your webhook Signing secret.
    </p>

    <p>
        When configuring your Webhook settings:
        <ul>
            <li>Add {!! route('payments.webhook', ['gateway' => 'stripe'])!!} as your webhook url</li>
            <li>Add <b>invoice.paid</b> event type to your webhook details</li>
        </ul>
    </p>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop