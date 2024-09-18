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
    <script src="{{ asset('assets/plugins/payments/braintree.js') }}"></script>
    <script>
        var form = document.querySelector('#payment-form');
        var client_token = "{{ $token }}";
        braintree.dropin.create({
            authorization: client_token,
            selector: '#bt-dropin',
            paypal: {
                flow: 'vault'
            }
        }, function (createErr, instance) {
            if (createErr) {
                console.log('Create Error', createErr);
                return;
            }
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                instance.requestPaymentMethod(function (err, payload) {
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