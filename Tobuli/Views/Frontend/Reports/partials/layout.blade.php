<!DOCTYPE html>
<html lang="{{ Language::iso() }}" dir="{{ Language::dir() == 'rtl' ? 'RTL' : 'LTR' }}" >
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        @if ($report->getFormat() == 'xls')
            <title>Report</title>
        @else
            <title>{{ Appearance::getSetting('server_name') }}</title>
            <style><?php require ( base_path('public/assets/css/report.css') ); ?></style>
            @yield('scripts')
            @yield('styles')
        @endif

    </head>

    <body class="reports">
        <div class="container">
            @if ($report->getFormat() == 'html')
            <header>
                <div class="header-left">
                    <div class="report-wrap">
                        <div class="report-logo" style="background: none;">
                            <img src="{{ Appearance::getAssetFileUrl('logo') }}" class="logo" alt="Logo">
                        </div>
                    </div>
                    <div class="report-curve"></div>
                </div>
                <div class="header-right"></div>
            </header>

            @elseif ($report->getFormat() != 'xls' && Appearance::assetFileExists('logo'))
                <img src="{{ Appearance::getAssetFilePath('logo') }}" class="logo" alt="Logo">
            @endif

            @yield('content')

        </div>
    </body>
</html>