@extends('Frontend.Layouts.frontend')

<video src="https://knoxgps.com/assets/video/4545.mp4" autoplay="true" muted="true" loop="false" poster=""></video>
@section('content')
    @if ( Appearance::getSetting('welcome_text') )
    <h1 class="sign-in-text text-center">
        {!! Appearance::getSetting('welcome_text') !!}
    </h1>
    @endif

    <div class="panel">
        <div class="panel-background"></div>
        <div class="panel-body">
            <a href="{{ route('home') }}">
                <img class="img-responsive center-block" src="{{ Appearance::getAssetFileUrl('logo-main') }}" alt="Logo">
            </a>
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

            {!! Form::open(array('route' => 'authentication.store', 'class' => 'form')) !!}
                <div class="form-group">
                    {!! Form::email('email', null, ['class' => 'form-control input-lg', 'placeholder' => trans('validation.attributes.email'), 'id' => 'sign-in-form-email']) !!}
                </div>
                <div class="form-group">
                    {!! Form::password('password', ['class' => 'form-control input-lg', 'placeholder' => trans('validation.attributes.password'), 'id' => 'sign-in-form-password']) !!}
                </div>

                @if (config('session.remember_me'))
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('remember_me', 1, ['id' => 'sign-in-form-remember']) !!}
                        <label>{!! trans('validation.attributes.remember_me') !!}</label>
                    </div>
                </div>
                @endif

                <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">{!! trans('front.sign_in') !!}</button>

                <hr>

                <div class="form-group">
                    <a href="{!! route('password_reminder.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.cant_sign_in') !!}</a>
                </div>
                @if (settings('main_settings.allow_users_registration'))
                <div class="form-group">
                    <a href="{!! route('registration.create') !!}" class="btn btn-block btn-lg btn-default">{!! trans('front.not_a_member') !!}</a>
                </div>
                @endif
            {!! Form::close() !!}
        </div>
    </div>

    @if ( Appearance::getSetting('google_play_link') || Appearance::getSetting('apple_store_link') )
        <div class="app-links">
            @if ( Appearance::getSetting('google_play_link') )
                <div class="col-xs-6">
                    <a href="{{ Appearance::getSetting('google_play_link') }}" target="_blank"><img src="{{ asset('assets/images/google-play.png') }}" class="img-responsive" /></a>
                </div>
            @endif

            @if ( Appearance::getSetting('apple_store_link') )
                <div class="col-xs-6">
                    <a href="{{ Appearance::getSetting('apple_store_link') }}" target="_blank"><img src="{{ asset('assets/images/apple-store.png') }}" class="img-responsive" /></a>
                </div>
            @endif
            <div class="clearfix"></div>
        </div>

    @endif

    @if ( Appearance::getSetting('bottom_text') )
        <p class="sign-in-text">{!! Appearance::getSetting('bottom_text') !!}</p>
    @endif
@stop
