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
        <div class="col-sm-offset-4 col-sm-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Mobile Direct Debit</div>
                </div>

                <div class="panel-body">
                    {!! Form::open([
                    'route'     => ['payments.subscribe', 'order_id' => $order_id, 'gateway' => $gateway],
                    'method'    => 'POST',
                    'id'        => 'payment-form'
                    ]) !!}

                        <div class="form-group">
                            {!!Form::label('phone', trans('validation.attributes.phone'))!!}
                            {!!Form::text('phone', null, ['class' => 'form-control'])!!}
                        </div>

                        <button type="submit" class="btn btn-primary form-control">Proceed</button>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@stop