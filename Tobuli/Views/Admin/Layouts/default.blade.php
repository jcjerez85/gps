<!DOCTYPE html>
<!--[if IE 8]> <html lang="{{ Language::iso() }}" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="{{ Language::iso() }}" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="{{ Language::iso() }}" class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8"/>
    <title>{{ Appearance::getSetting('server_name') }}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="app-version" content="{{ config('tobuli.version') }}">
    <meta name="app-build" content="{{ config('app.build') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link rel="shortcut icon" href="{{ Appearance::getAssetFileUrl('favicon') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset_resource('assets/css/'.Appearance::getSetting('template_color').'.css') }}" />

    @yield('styles')
</head>

<body class="admin-layout">

<div class="header">
    <nav class="navbar navbar-main navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-header-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                @if ( Appearance::assetFileExists('logo') )
                <a class="navbar-brand" href="javascript:"><img src="{{ Appearance::getAssetFileUrl('logo') }}"></a>
                @endif

                <p class="navbar-text">ADMIN</p>
            </div>

            <div class="collapse navbar-collapse" id="bs-header-navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    {!! getNavigation() !!}
                </ul>
            </div>
        </div>
    </nav>
</div>

<div class="content">
    <div class="container-fluid">
        @if (Session::has('success'))
            <div class="alert alert-success">
                {!! Session::get('success') !!}
            </div>
        @endif
        @if (Session::has('error'))
            <div class="alert alert-danger">
                {!! Session::get('error') !!}
            </div>
        @endif

        @yield('content')
    </div>
</div>

<div id="footer">
    <div class="container-fluid">
        <p>
            <span>{{ date('Y') }} &copy; {{ Appearance::getSetting('server_name') }}
            | {{ CustomFacades\Server::ip() }}
            | v{{ config('tobuli.version') }}
            @if (Auth::User()->isAdmin())
                @if ( $limit = CustomFacades\Server::getDeviceLimit())
                     | {{ "1-$limit " . strtolower(trans('front.objects')) }}
                @endif

                | {{ trans('front.last_update') }}: {{ Formatter::time()->human(CustomFacades\Server::lastUpdate()) }}

                @if ( ! CustomFacades\Server::isAutoDeploy())
                    | <i style="color: red;">Automatic updates disabled</i>
                @endif
                @if (CustomFacades\Server::isSpacePercentageWarning())
                    | <i style="color: red;">Server disk space is almost full</i>
                @endif
            @endif
            </span>
        </p>
    </div>
</div>

@include('Frontend.Layouts.partials.trans')

<script src="{{ asset_resource('assets/js/core.js') }}"></script>
<script src="{{ asset_resource('assets/js/app.js') }}"></script>

@include('Frontend.Layouts.partials.app')

@yield('javascript')
@stack('javascript')

<script>
    $.ajaxSetup({cache: false});
    window.lang = {
        nothing_selected: '{{ trans('front.nothing_selected') }}',
        color: '{{ trans('validation.attributes.color') }}',
        from: '{{ trans('front.from') }}',
        to: '{{ trans('front.to') }}',
        add: '{{ trans('global.add') }}'
    };
    app.lang = {!! json_encode(Language::get()) !!};
    app.initSocket();
</script>

<div class="modal" id="modalDeleteConfirm">
    <div class="contents">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title thin" id="modalConfirmLabel">{{ trans('admin.delete') }}</h3>
                </div>
                <div class="modal-body">
                    <p>{{ trans('admin.do_delete') }}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-main" onclick="modal_delete.del();">{{ trans('admin.yes') }}</button>
                    <button class="btn btn-side" data-dismiss="modal" aria-hidden="true">{{ trans('global.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="js-confirm-link" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                loading
            </div>
            <div class="modal-footer" style="margin-top: 0">
                <button type="button" value="confirm" class="btn btn-main submit js-confirm-link-yes">{{ trans('admin.confirm') }}</button>
                <button type="button" value="cancel" class="btn btn-side" data-dismiss="modal">{{ trans('admin.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="modalError">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title thin" id="modalErrorLabel">{{ trans('global.error_occurred') }}</h3>
            </div>
            <div class="modal-body">
                <p class="alert alert-danger"></p>
            </div>
            <div class="modal-footer">
                <button class="btn default" data-dismiss="modal" aria-hidden="true">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalSuccess">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 class="modal-title thin" id="modalSuccessLabel">{{ trans('global.warning') }}</h3>
            </div>
            <div class="modal-body">
                <p class="alert alert-success"></p>
            </div>
            <div class="modal-footer">
                <button class="btn default" data-dismiss="modal" aria-hidden="true">{{ trans('global.close') }}</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>

 