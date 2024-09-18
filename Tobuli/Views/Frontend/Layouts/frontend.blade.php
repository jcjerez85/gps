<!doctype html>
<html lang="{{ Language::iso() }}" class="no-js" itemscope itemtype="http://schema.org/WebSite">
<head>
    @include('Frontend.Layouts.partials.head')
    @yield('styles')

    <style>
        @if ( Appearance::getSetting('login_page_background_color') )
        body.sign-in-layout { background-color: {{ Appearance::getSetting('login_page_background_color') }}; }
        @endif

        @if ( Appearance::getSetting('login_page_text_color') )
        body.sign-in-layout .sign-in-text { color: {{ Appearance::getSetting('login_page_text_color') }}; }
        @endif

        @if ( Appearance::assetFileExists('background') )
        body.sign-in-layout { background-image: url( {!! Appearance::getAssetFileUrl('background') !!} ); }
        video {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2;
        }
        @endif

        @if ( Appearance::getSetting('login_page_panel_background_color') )
        body.sign-in-layout .panel-background { background: {{ Appearance::getSetting('login_page_panel_background_color') }}; }
        
	@endif

        @if ( Appearance::getSetting('login_page_panel_transparency') != null )
        <?php $opacity = 1 - (int)Appearance::getSetting('login_page_panel_transparency') / 100; ?>
        body.sign-in-layout .panel-background { opacity: {{ $opacity }}; }
        @endif
    </style>
</head>

<!--[if IE 8 ]><body class="ie8 sign-in-layout"> <![endif]-->
<!--[if IE 9 ]> <body class="ie9 sign-in-layout"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><body class="sign-in-layout"><!--<![endif]-->

<div class="center-vertical">
    <div class="container">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>
