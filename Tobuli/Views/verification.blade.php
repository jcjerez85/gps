@extends('Frontend.Layouts.frontend')

@section('content')
    <div class="panel">
        <div class="panel-background"></div>
        <div class="panel-body">

            @if ( Appearance::assetFileExists('logo-main') )
                <a href="{{ route('home') }}">
                    <img class="img-responsive center-block" src="{{ Appearance::getAssetFileUrl('logo-main') }}" alt="Logo">
                </a>
            @endif

            <hr>

            <div class="alert alert-success alert-dismissible">
                {{ trans('front.please_verify_email') }}
            </div>

            <hr>

             {!! Form::open(array('url' => $url, 'method' => 'get')) !!}
            <button class="btn btn-lg btn-primary btn-block" type="Submit">{!! trans('global.continue') !!}</button>
            {!! Form::close() !!}
        </div>
    </div>
@stop