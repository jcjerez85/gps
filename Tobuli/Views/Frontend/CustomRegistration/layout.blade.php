<!doctype html>
<html lang="{{ Language::iso() }}" class="no-js" itemscope itemtype="http://schema.org/WebSite">
<head>
    @include('Frontend.Layouts.partials.head')
    @yield('styles')

    <style>
        @if ( Appearance::getSetting('login_page_background_color') )
        body.sign-in-layout { background-color: {{ Appearance::getSetting('login_page_background_color') }}; }
        @endif

        @if ( Appearance::assetFileExists('background') )
        body.sign-in-layout { background-image: url( {!! Appearance::getAssetFileUrl('background') !!} ); }
        @endif

        .content-heading {
            margin: 15px;
            text-align: center;
            font-size: 20px;
            font-weight: 300;
        }

        .plan {
            box-shadow: none !important;
        }
        .plan:hover {
            cursor: pointer;
        }
        .plan.active {
            border-color: #1b99bd;
        }
        .plan .plan-title {
            float: none;
            font-weight: 600;
            font-size: larger;
            text-align: center;
            white-space: normal;
        }
        .plan .plan-price {
             font-size: 30px;
             text-align: center;
        }
        .plan .plan-currency {
            font-size: 50%;
        }
        .plan .plan-duration-disclamer {
            font-size: 10px;
            text-align: center;
        }
        .review-block {
            font-size: larger;
        }
        .review-block img {
            max-width: 100%;
        }
        .media-object {
            max-width: 120px;
        }
    </style>
</head>

<!--[if IE 8 ]><body class="ie8 sign-in-layout"> <![endif]-->
<!--[if IE 9 ]> <body class="ie9 sign-in-layout"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><body class="sign-in-layout"><!--<![endif]-->

<div class="center-vertical">
    <div class="container">
        <div class="col-xs-12 col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset_resource('assets/js/core.js') }}" type="text/javascript"></script>
<script src="{{ asset_resource('assets/js/app.js') }}"></script>
@yield('scripts')

</body>
</html>