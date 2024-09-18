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

@section('styles')
    <style>
        .StripeElement {
            background-color: white;
            height: 40px;
            padding: 10px 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
@stop

@section('content')
    @if (Session::has('message'))
        <div class="alert alert-danger alert-dismissible">
            {!! Session::get('message') !!}
        </div>
    @endif
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            {!! Session::get('success') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-offset-2 col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Stripe</div>
                </div>

                <div class="panel-body">
                    <div class="col-lg-offset-2 col-lg-8">
                        {!! Form::open([
                        'route'     => ['payments.subscribe', 'order_id' => $order_id, 'gateway' => $gateway],
                        'method'    => 'POST',
                        'id'        => 'payment-form'
                        ]) !!}

                            <label for="card-element">Credit or debit card</label>
                            {{-- A Stripe Element will be inserted here. --}}
                            <div id="card-element" class="form-control"></div>
                            <div id="card-errors" role="alert"></div>
                            <input type="hidden" name="intent" value="{{ $payment_intent->id }}">

                            <button type="submit" class="btn btn-primary form-control">Proceed</button>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('self-scripts')
    {{-- It should always be loaded directly from https://js.stripe.com --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Create a Stripe client.
        var stripe = Stripe('{{ $public_key }}');

        // Create an instance of Elements.
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        // (Note that this demo uses a wider set of styles than the guide below.)
        var style = {
            base: {
                color: '#32325d',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var url = '{!! route('payments.subscribe', ['gateway' => $gateway, 'order_id' => $order_id]) !!}';

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element.
        card.addEventListener('change', function (event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            createPaymentIntent(card);
        });

        function createPaymentIntent(card) {
            loader.add($('body'));

            stripe
                .confirmCardPayment('{!! $payment_intent->client_secret !!}', {
                    payment_method: {
                        card: card,
                        billing_details: {
                            email: '{!! Auth::user()->email !!}',
                            name: '{!! Auth::user()->email !!}',
                        }
                    },
                })
                .then(function(result) {
                    var displayError = document.getElementById('card-errors');

                    if (result.error) {
                        displayError.textContent = result.error.message;
                        loader.remove($('body'));
                    } else {
                        stripe.createSource(card).then(function(result) {
                            if (result.error) {
                                var errorElement = document.getElementById('card-errors');
                                errorElement.textContent = result.error.message;
                                loader.remove($('body'));
                            } else {
                                stripeSourceHandler(result.source);
                                document.getElementById('payment-form').submit();
                            }
                        });
                    }
                });
        }

        function stripeSourceHandler(source) {
            // Insert the source ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeSource');
            hiddenInput.setAttribute('value', source.id);
            form.appendChild(hiddenInput);
        }
    </script>
@stop