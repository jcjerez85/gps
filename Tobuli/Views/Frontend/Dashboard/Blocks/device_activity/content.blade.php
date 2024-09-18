<style>#device_activity .error{display:none !important;}</style>
<div id="device_activity" style="width: 200px; height: 300px; margin: auto;"></div>
<script type='text/javascript'>
    $(document).ready(function () {
        $.plot('#device_activity',
            [
                {
                    label: '{{ trans('global.online') }} - {{ $online }}',
                    data: {{ $online }},
                    color: '#59FFB4'
                },
                {
                    label: '{{ trans('global.offline') }} - {{ $offline }}',
                    data: {{ $offline }},
                    color: '#FF6363'
                }
            ],
            {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        tilt: 0.5,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.8
                            }
                        },
                        combine: {
                            color: '#999',
                            threshold: 0.1
                        }
                    }
                },
                legend: {
                    show: false
                }
            }

            // {
            //     series: {
            //         pie: {
            //             innerRadius: 0.35,
            //             show: true
            //         }
            //     },
            //     legend: {
            //         show: false,
            //     },
            // }
        );
        function labelFormatter(label, series) {
            return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;font-weight:bold;text-shadow: 0px 0px 5px #000;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
            // return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;font-weight:bold;text-shadow: 0px 0px 5px #000;'>" + label + "</div>";
        }
        $('#device_activity').css('width', 'auto');
    });
</script>