@extends('Frontend.Layouts.default')

@section('header-menu-items')
    @if ( Auth::User() )
        <li>
            <a href="{{ route('logout') }}">
                <i class="icon logout"></i> <span class="text">{{ trans('global.log_out') }}</span>
            </a>
        </li>
    @endif
@stop

@section('content')
    <div class="row">
        <div class="col-lg-offset-2 col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Braintree</div>
                </div>

                <div class="panel-body">
                    <div class="col-lg-offset-2 col-lg-8">

                        <div class="billing-info" >
                            <div class="form-group">
                                <label for="billing-phone">{{ trans('front.billing_phone') }}</label>
                                <input type="billing-phone" class="form-control" id="billing-phone" placeholder="123-456-7890">
                            </div>
                            <div class="form-group">
                                <label for="billing-given-name">{{ trans('front.billing_firstname') }}</label>
                                <input type="billing-given-name" class="form-control" id="billing-given-name" placeholder="First">
                            </div>
                            <div class="form-group">
                                <label for="billing-surname">{{ trans('front.billing_surname') }}</label>
                                <input type="billing-surname" class="form-control" id="billing-surname" placeholder="Last">
                            </div>
                            <div class="form-group">
                                <label for="billing-street-address">{{ trans('front.billing_street') }}</label>
                                <input type="billing-street-address" class="form-control" id="billing-street-address" placeholder="123 Street">
                            </div>
                            <div class="form-group">
                                <label for="billing-extended-address">{{ trans('front.billing_extented_address') }}</label>
                                <input type="billing-extended-address" class="form-control" id="billing-extended-address" placeholder="Unit 1">
                            </div>
                            <div class="form-group">
                                <label for="billing-city">{{ trans('front.billing_city') }}</label>
                                <input type="billing-city" class="form-control" id="billing-city" placeholder="City">
                            </div>
                            <div class="form-group">
                                <label for="billing-state">{{ trans('front.billing_state') }} state</label>
                                <input type="billing-state" class="form-control" id="billing-state" placeholder="State">
                            </div>
                            <div class="form-group">
                                <label for="billing-postal-code">{{ trans('front.billing_postal_code') }}</label>
                                <input type="billing-postal-code" class="form-control" id="billing-postal-code" placeholder="12345">
                            </div>
                            <div class="form-group">
                                <label for="billing-country-code">{{ trans('front.billing_country_code') }}</label>
                                <input type="billing-country-code" class="form-control" id="billing-country-code" placeholder="XX">
                            </div>
                        </div>

                        <div class="checkout">
                            {!! Form::open([
                                'route'     => ['payments.subscribe', 'order_id' => $order_id, 'gateway' => $gateway],
                                'method'    => 'POST',
                                'id'        => 'payment-form'
                            ]) !!}

                            <div class="bt-drop-in-wrapper">
                                <div id="bt-dropin"></div>
                            </div>

                            {!! Form::hidden('payment_method_nonce', null, ['id' => 'nonce']) !!}
                            {!! Form::hidden('customer_id', $customer_id) !!}

                            <button type="submit" class="btn btn-primary form-control" style="margin-top: 15px">
                                Proceed
                            </button>

                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src='https://js.braintreegateway.com/web/dropin/1.26.0/js/dropin.min.js'></script>
    <script>
        var form = document.querySelector('#payment-form');
        var client_token = "{{ $token }}";
        var billingFields = [
            'billing-phone',
            'billing-given-name',
            'billing-surname',
            'billing-street-address',
            'billing-extended-address',
            'billing-city',
            'billing-state',
            'billing-postal-code',
            'billing-country-code'
        ].reduce(function (fields, fieldName) {
            var field = fields[fieldName] = {
                input: document.getElementById(fieldName),
            };

            field.input.addEventListener('focus', function() {
                clearFieldValidations(field);
            });

            if ('billing-extended-address' == fieldName)
                field.optional = true;

            return fields;
        }, {});

        function clearFieldValidations (field) {
            field.input.parentNode.classList.remove('has-error');
        }

        function validateBillingFields() {
            var isValid = true;

            Object.keys(billingFields).forEach(function (fieldName) {
                var fieldEmpty = false;
                var field = billingFields[fieldName];

                if (field.optional) {
                    return;
                }

                fieldEmpty = field.input.value.trim() === '';

                if (fieldEmpty) {
                    isValid = false;
                    field.input.parentNode.classList.add('has-error');
                } else {
                    clearFieldValidations(field);
                }
            });

            return isValid;
        }

        var dropin = braintree.dropin.create({
            authorization: client_token,
            selector: '#bt-dropin',
            threeDSecure: true,
        }, function (createErr, instance) {

            if (createErr) {
                console.log('Create Error', createErr);
                return;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if ( ! validateBillingFields())
                    return;

                instance.requestPaymentMethod({
                    threeDSecure: {
                        amount: '{{ $amount }}',
                        email: '{{ $email }}',
                        billingAddress: {
                            givenName: billingFields['billing-given-name'].input.value,
                            surname: billingFields['billing-surname'].input.value,
                            phoneNumber: billingFields['billing-phone'].input.value.replace(/[\(\)\s\-]/g, ''), // remove (), spaces, and - from phone number
                            streetAddress: billingFields['billing-street-address'].input.value,
                            extendedAddress: billingFields['billing-extended-address'].input.value,
                            city: billingFields['billing-city'].input.value,
                            state: billingFields['billing-state'].input.value,
                            postalCode: billingFields['billing-postal-code'].input.value,
                            countryCodeAlpha2: billingFields['billing-country-code'].input.value
                        }
                    }
                }, function (err, payload) {
                    if (err) {
                        console.log('Request Payment Method Error', err);
                        return;
                    }
                    // Add the nonce to the form and submit
                    document.querySelector('#nonce').value = payload.nonce;

                    form.submit();
                });
            });
        });
    </script>
@stop