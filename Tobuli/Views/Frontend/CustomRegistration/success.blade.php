@extends('Frontend.CustomRegistration.layout')

@section('content')
    <div class="panel panel-default" id="custom-registration-payment-success">
        <div class="panel-background"></div>

        <div class="panel-body">
            <h4 class="text-center">Your tracker was setup successfully</h4>

            <div class="alert alert-success" role="alert">
                <i class="icon check"></i> {{ trans('front.payment_received') }}
            </div>

            <div class="row">
            <div class="col-sm-5">

                {!! trans('front.custom_payment_success') !!}

                <hr>

                <a href="{{ route('home') }}" class="btn btn-block btn-primary">
                    View tracking platform
                </a>

                <a href="{{ route('register.step.create', 'device') }}" class="btn btn-block btn-secondary">
                    Setup another device
                </a>

                <br>
            </div>
            <div class="col-sm-7">
                <ul class="list-group">
                    <li class="list-group-item text-center"><b>{{ trans('front.order_summary') }}</b></li>
                    <li class="list-group-item">
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
                                <div class="pull-right">
                                    <b>{{ $order->plan->formatPriceDuration() }}</b>
                                </div>
                                {{ $order->plan->title }}
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <b>{{ trans('front.total_charged') }}</b>
                        <b class="pull-right">{{ settings('currency.symbol') . $order->price }}</b>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </div>
@endsection