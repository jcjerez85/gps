<div class="panel panel-transparent">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="text-center">
                {{ trans('admin.total_devices') }}: <b>{{ $total }}</b>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div id="device-statuses-graph" style="width: 100%; height: 200px"></div>
    </div>
</div>

<style>
    .flotTip {
        padding: 3px 5px;
        background-color: #000;
        z-index: 9999;
        color: #fff;
        opacity: .80;
        filter: alpha(opacity=85);
    }
</style>

<script type='text/javascript'>
    $(document).ready(function () {
        $.plot('#device-statuses-graph',
                {!! json_encode($statuses) !!},
                {
                    series: {
                        pie: {
                            show: true,
                            label: false,
                            stroke: {
                                color: "#ccc",
                                width: 1
                            },
                            radius: 0.8,
                            tilt: 0.5
                        }
                    },
                    grid: {
                        hoverable: true
                    },
                    tooltip: true,
                    tooltipOpts: {
                        cssClass: "flotTip",
                        content: "%p.0%, %s",
                        shifts: {
                            x: 20,
                            y: 0
                        },
                        defaultTheme: false
                    },
                    legend: {
                        show: true
                        /*legendFormatter: function (label, series) {
                         return '<div ' +
                         'style="font-size:8pt;text-align:center;padding:2px;">' +
                         label + ' ' + Math.round(series.percent)+'%</div>';
                         }*/
                    }
                });

        $("#device-statuses-graph").css('width', 'auto');
    });
</script>