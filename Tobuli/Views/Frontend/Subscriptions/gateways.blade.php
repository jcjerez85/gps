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
        .gateway-img {
            max-height: 45px;
            margin: auto;
        }

        .box {
            padding: 20px;
        }

        .methods {
            display: block;
            margin: 0 auto;
            text-align: center;
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
        <div class="col-lg-offset-2 col-sm-8">
            <div class="panel panel-default" id="select_gateway">
                <div class="panel-heading">
                    <div class="panel-title">{!! trans('front.select_gateway') !!}</div>
                </div>

                <div class="panel-body">
                    @if(empty($gateways))
                        <p>{{ trans('admin.billing_gateway') . ' ' . lcfirst(trans('global.not_found')) }}</p>
                    @else
                        <div class="row">
                            @foreach($gateways as $gateway)
                                <div class="col-lg-6 box">
                                    <a href="{{ route('payments.checkout', ['gateway' => $gateway, 'order_id' => $order_id]) }}">
                                        <img src="{{ asset(config('payments.' . $gateway . '.logo')) }}"
                                             class="gateway-img img-responsive">
                                        <small class="methods">{{ config('payments.' . $gateway . '.description') }}</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop