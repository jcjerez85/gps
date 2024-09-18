<!DOCTYPE html>
<html lang="{{ Language::iso() }}">
<head>
    @include('Frontend.Layouts.partials.head')
    @yield('styles')

</head>
<body>
@include('Frontend.Layouts.partials.loading')

<div id="map"></div>
<div id="map-controls">
    <div>
        <div class="btn-group-vertical" data-position="fixed" role="group">
            <button type="button" class="btn" onClick="$('.leaflet-control-layers').toggleClass('leaflet-control-layers-expanded');">
                <span class="icon map-change"></span>
            </button>
        </div>
    </div>
</div>

@yield('self-scripts')

<script src="{{ asset_resource('assets/js/core.js') }}" type="text/javascript"></script>
<script src="{{ asset_resource('assets/js/app.js') }}" type="text/javascript"></script>

@include('Frontend.Layouts.partials.trans')
@include('Frontend.Layouts.partials.app')

<script>
    $(window).on("load", function() {
        //$('a[href="#gps-device-street-view-large"]').addClass('disabled');

        app.follow({!! json_encode($item) !!});
    });
</script>
</body>