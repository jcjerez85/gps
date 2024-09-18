<!DOCTYPE html>
<html lang="{{ Language::iso() }}">
<head>
    @include('Frontend.Layouts.partials.head')
    @yield('styles')
</head>
<body>

<div id="header">
    <nav class="navbar navbar-main">
        <div class="container-fluid">
            <div class="navbar-header">
                @if ( Appearance::assetFileExists('logo') )
                    <a class="navbar-brand" href="/" title="{{ Appearance::getSetting('server_name') }}"><img src="{{ Appearance::getAssetFileUrl('logo') }}"></a>
                @endif
            </div>

            <ul class="nav navbar-nav navbar-right">

                @yield('header-menu-items')

                <li class="dropdown">
                    <a href="javascript:" class="dropdown-toggle" role="button" id="dropMyAccount" data-toggle="dropdown" rel="tooltip" data-placement="bottom" title="{!!trans('front.my_account')!!}">
                        <span class="icon account"></span>
                        <span class="text">{!!trans('front.my_account')!!}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropMyAccount">
                        <li>
                            <a href="javascript:" data-url="{{ route('subscriptions.index') }}" data-modal="subscriptions_edit">
                                <span class="icon membership"></span>
                                <span class="text">{!!trans('front.subscriptions')!!}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{!!route('logout')!!}">
                                <span class="icon logout"></span>
                                <span class="text">{!!trans('global.log_out')!!}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="language-selection">
                    <a href="javascript:" data-url="{{ route('languages.index') }}" data-modal="language-selection">
                        <span class="icon">
                            <img src="{{ Language::flag() }}" alt="Language" class="img-thumbnail">
                        </span>
                        <span class="text">{!!trans('global.language')!!}</span>
                    </a>
                </li>
            </ul>

        </div>
    </nav>
</div>

<div class="content">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>

@include('Frontend.Layouts.partials.trans')

@yield('self-scripts')

<script src="{{ asset_resource('assets/js/core.js') }}" type="text/javascript"></script>
<script src="{{ asset_resource('assets/js/app.js') }}" type="text/javascript"></script>

@yield('scripts')

</body>
</html>