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

{!! Form::open([
        'route'     => ['register.step.store', 'step' => 'checkout'],
        'method'    => 'POST',
        'id'        => 'payment-form'
        ]) !!}

{!! Form::hidden('order_id', $order->id) !!}
<input type="hidden" name="intent" value="{{ $payment_intent->id }}">
<div class="row">
    <div class="col-xs-12 col-sm-offset-2 col-sm-8">

        <div class="content-heading">
            Enter your payment information
        </div>

        <ul class="list-group">
            <li class="list-group-item text-center"><b>{{ trans('front.order_summary') }}</b></li>
            <li class="list-group-item">

                <div class="media">
                    @if ($order->entity->deviceType)
                    <div class="media-left">
                        <img class="media-object" src="{{ $order->entity->deviceType->getImageUrl() }}">
                    </div>
                    @endif
                    <div class="media-body">
                        <h4 class="media-heading">
                            {{ $order->entity->deviceType ? $order->entity->deviceType->title : $order->entity->name }}
                        </h4>
                        <div>
                            <div class="pull-right">{{ $order->plan->formatPriceDuration() }}</div>
                            {{ $order->plan->title }}
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <b>{{ trans('global.total') }}</b>
                <b class="pull-right">{{ settings('currency.symbol') . $order->price }}</b>
            </li>
            <li class="list-group-item">
                <div id="card-element" class="form-control"></div>
                <div id="card-errors" role="alert"></div>
            </li>
            <li class="list-group-item">
                <button type="submit" class="btn btn-primary form-control">{{ trans('front.checkout_secure') }}</button>
            </li>
        </ul>
    </div>
</div>
{!! Form::close() !!}

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ settings('payments.stripe.public_key') }}');
    var elements = stripe.elements();

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

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                createPaymentIntent(card);
            }
        });
    });

    function createPaymentIntent(card) {
        loader.add($('body'));

        stripe
            .confirmCardPayment('{!! $payment_intent->client_secret !!}', {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: '{!! Auth::user()->email !!}',
                    },
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