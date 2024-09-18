@extends('Frontend.Reports.partials.layout')

@section('styles')
    <style><?php require ( base_path('public/assets/css/report-map.css') ); ?></style>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/report.js') }}" type="text/javascript"></script>

    <script>
        var app = {
            settings: {
                showTraffic: false
            }
        };

        var startIcon = L.icon({
            iconUrl: '{{ asset('assets/images/route_start.png') }}',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });

        var endIcon = L.icon({
            iconUrl: '{{ asset('assets/images/route_end.png') }}',
            iconSize: [32, 32],
            iconAnchor: [16, 32]
        });
    </script>
@stop

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else
                <div class="panel-body">
                <div class="row">
                    @if( ! empty($item['map']))
                    <div class="col-md-6">
                        <div style="overflow: hidden; position: relative; width: 100%;height: 300px;">
                            <?php $uid = 'map-' . uniqid(); ?>
                            <div id="{{ $uid }}" style="overflow: hidden;position: absolute;top: 0;bottom: 0;left: 0;right: 0;"></div>
                            <script>
                                var map = L.map("{{ $uid }}", {
                                    zoomControl: false,
                                    attributionControl: false,
                                    boxZoom: false,
                                });
                                new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18}).addTo(map);

                                L.marker({{ json_encode($item['map']['start']) }}, {icon: startIcon}).addTo(map);
                                L.marker({{ json_encode($item['map']['end']) }}, {icon: endIcon}).addTo(map);

                                var polylines = L.featureGroup();

                                @foreach ($item['map']['polylines'] as $polyline)
                                    var latlngs = {{ json_encode($polyline['latlngs']) }};
                                    var polyline = L.polyline(latlngs, {color: '{{ $polyline['color'] }}'}).addTo(map);

                                    polylines.addLayer(polyline);
                                @endforeach

                                map.fitBounds(polylines.getBounds());
                            </script>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        @if( ! empty($item['totals']))
                            <table class="table">
                                <tr>
                                    <td class="col-sm-6">
                                        @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['start', 'end', 'distance', 'drive_duration', 'stop_duration', 'speed_max', 'speed_avg', 'overspeed_count', 'underspeed_count', 'stop_count']])
                                    </td>
                                    <td class="col-sm-6">
                                        @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['fuel_consumption_list', 'fuel_price_list', 'engine_hours', 'engine_work', 'engine_idle', 'odometer', 'drivers']])
                                    </td>
                                </tr>
                            </table>
                        @endif
                    </div>
                </div>
                </div>
            @endif
        </div>
    @endforeach
@stop