@extends('Frontend.Layouts.frontend')

@section('content')
    <h1 class="sign-in-text text-center">{{ trans('front.not_a_member') }}</h1>

    <div class="panel">
        <div class="panel-background"></div>
        <div class="panel-body">

            @if ( Appearance::assetFileExists('logo-main') )
            <a href="{{ route('home') }}">
                <img class="img-responsive center-block" src="{{ Appearance::getAssetFileUrl('logo-main') }}" alt="Logo">
            </a>
            @endif

            <hr>

            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible">
                    {!! Session::get('success') !!}
                </div>
            @endif

            @if (Session::has('message'))
                <div class="alert alert-danger alert-dismissible">
                    {!! Session::get('message') !!}
                </div>
            @endif

            {!! Form::open(array('route' => 'registration.store', 'class' => 'form', 'id' => 'registration-form')) !!}
            {!! error_for('id', $errors) !!}
            <div class="form-group">
                {!! Form::email('email', null, ['class' => 'form-control input-lg', 'placeholder' => trans('validation.attributes.email'), 'id' => 'sign-in-form-email']) !!}
                {!! error_for('email', $errors) !!}
            </div>

            @include('Frontend.Captcha.form')

            <button type="submit" class="btn btn-lg btn-primary btn-block">{{ trans('front.register') }}</button>

            <hr>

            <div class="form-group">
                <a href="{!! route('password_reminder.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.cant_sign_in') !!}</a>
            </div>

            <div class="form-group">
                <a href="{!! route('authentication.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.sign_in') !!}</a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop