<div class="widget widget-sensor-graph" id="widget-fuel-graph" >
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon fuel_tank"></i> {{ trans('front.fuel') }}
        </div>
    </div>

    <div class="widget-body">
        @if (empty($data))
            <div class="widget-empty" style="display: block">
                <p>{{ $error ?? '' }}</p>
            </div>
        @else
            @foreach($data as $sensor)
            <div id="sensor-graph-{{ $sensor['id'] }}" class="sensor-graph"></div>
            <script>
                options.yaxis.tickFormatter = function(value, axis) {
                    return value.toFixed(axis.tickDecimals) + '{!! $sensor['unit'] !!}';
                };
                var graph_items = [];
                $.each({!! json_encode($sensor['values']) !!}, function(index, value) {
                    var date = moment.unix(value.t).toDate().getTime();
                    graph_items.push([date, value.v, 0, index]);
                });
                plots['{{ $sensor['id'] }}'] = $.plot("#sensor-graph-{{ $sensor['id'] }}", [graph_items], options);
            </script>
            @endforeach
        @endif
    </div>

    <script>
        var plots = {};
        var options = {
            colors: ["rgba(76, 84, 84, 1)"],
            lines: {
                show: true,
                lineWidth: 1.5,
                lineColor: 'red',
                fill: true,
                fillColor: "rgba(76, 84, 84, 0.4)"
            },
            xaxis: {
                minTickSize: [30, "minute"],
                mode: 'time',
                twelveHourClock: false
            },
            yaxis: {
                tickFormatter: function(value, axis) { return value.toFixed(axis.tickDecimals) + 'L'; },
                minTickSize: 1,
                tickDecimals: 0,
                zoomRange: false
            },
            grid: {
                color: "#999999",
                margin: {
                    top: 0,
                    left: 0,
                    bottom: 0,
                    right: 5
                },
                hoverable: false,
                borderWidth: 1,
                borderColor: '#DDDDDD'
            }
        };
    </script>
</div>