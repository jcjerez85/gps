<div class="row">
    <div class="col-sm-10">
        <div id="distance_travelled" style="width: 100%; height: 300px"></div>
    </div>
    <div class="col-sm-2">
        <div id="distance_travelled_legends"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var keys = [];
        var dates = {!! $keys !!};
        for (i in dates) {
            keys.push([i, dates[i]])
        }

        var dataset = [];
        var data = {!! $data !!};
        for (device in data) {
            dataset.push({
                label: device,
                data: data[device]
            });
        }

        $.plot($("#distance_travelled"), dataset, {
            yaxis: {
                font: {
                    size: 12,
                    color: "black",
                },
                tickFormatter: function formatter(x) {
                    return x.toString() + '{{ Formatter::distance()->getUnit() }}';
                }
            },
            xaxis: {
                ticks: keys,
                autoscaleMargin: .05,
                font: {
                    size: 12,
                    color: "black"
                }
            },
            series: {
                shadowSize: 1,
                bars: {
                    show: true,
                    barWidth: 0.06,
                    order: 1,
                    lineWidth: 0,
                    fill: true,
                    fillColor: { colors: [ { opacity: 1 }, { opacity: 0.5 } ] }
                }
            },
            legend: {
                show: true,
                noColumns: 1,
                labelFormatter: function(label, series) {
                    return '<span>' + label + '</span>';
                },
                container: $('#distance_travelled_legends'),
                labelBoxBorderColor: '#fff'
            },
            grid: {
                show: true,
                borderWidth: 0,
                borderColor: 'black',
                backgroundColor: '#fbfcfd',
            }
        });
    });
</script>
