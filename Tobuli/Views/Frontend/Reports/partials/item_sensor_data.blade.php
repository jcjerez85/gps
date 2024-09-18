@php
    if (!isset($graphWidth)) {
        $graphWidth = 12;
    }

    if (!isset($statsBlockWidth)) {
        $statsBlockWidth = 4;
    }
@endphp

<div class="row">
    <div class="col-md-{{ $graphWidth }}">
        <div class="graph-1-wrap" style="height:190px">
            <div class="graph-control">
                <li><strong>{{ trans('front.sensor') }}:</strong> {{ $sensor['name'] }}</li>
                <li class="graph-control-buttons" id="{{ $sensor['id'] }}-panRight" style="padding-top: 4px"><img src="{{ asset('assets/images/arrow_right.gif') }}" border="0"></li>
                <li class="graph-control-buttons" id="{{ $sensor['id'] }}-panLeft" style="padding-top: 4px"> <img src="{{ asset('assets/images/arrow_left.gif') }}" border="0"></li>
                <li class="graph-control-buttons" id="{{ $sensor['id'] }}-zoomIn"> <img src="{{ asset('assets/images/zoom_in.png') }}" style="margin-top:4px;" border="0"></li>
                <li class="graph-control-buttons" id="{{ $sensor['id'] }}-zoomOut"><img src="{{ asset('assets/images/zoom_out.png') }}" style="margin-top:4px;" border="0"></li>
                <li class="graph-control-label" id="{{ $sensor['id'] }}-labeler">
                    <div style="padding-top: 5px">
                        <span id="{{ $sensor['id'] }}-hoverdata-kmh" style="font-weight: bold"></span>
                        <span id="{{ $sensor['id'] }}-hoverdata-date"></span>
                    </div>
                </li>
            </div>
            <div id="{{ $sensor['id'] }}-placeholder" class="demo-placeholder">

            </div>
            <script>
                options.yaxis.tickFormatter = function(value, axis) {
                    return value.toFixed(axis.tickDecimals) + '{!! $sensor['unit'] !!}';
                };
                var items = {!! json_encode($sensor['values']) !!};
                var graph_items = [];
                $.each(items, function(index, value) {
                    var date = moment.unix(value.t).toDate().getTime();
                    graph_items.push([date, value.v, 0, index]);
                });
                plots['{{ $sensor['id'] }}'] = $.plot("#{{ $sensor['id'] }}-placeholder", [graph_items], options);

                $("#{{ $sensor['id'] }}-placeholder").bind("plothover", function (event, pos, item) {
                    if (item != null) {
                        var strKmh = item.datapoint[1] + " {!! $sensor['unit'] !!}";
                        var fixDate = moment(item.datapoint[0]).format('YYYY-MM-DD HH:mm:ss');

                        $("#{{ $sensor['id'] }}-hoverdata-kmh").text(strKmh);
                        $("#{{ $sensor['id'] }}-hoverdata-date").text(' - ' + fixDate);
                    }
                });

                $("#{{ $sensor['id'] }}-panLeft").on('click', function(e){
                    e.preventDefault();
                    plots['{{ $sensor['id'] }}'].pan({
                        left: -100
                    })
                });

                $("#{{ $sensor['id'] }}-panRight").on('click', function(e){
                    e.preventDefault();
                    plots['{{ $sensor['id'] }}'].pan({
                        left: +100
                    })
                });

                $("#{{ $sensor['id'] }}-zoomIn").on('click', function(e){
                    e.preventDefault();
                    plots['{{ $sensor['id'] }}'].zoom();
                });

                $("#{{ $sensor['id'] }}-zoomOut").on('click', function(e){
                    e.preventDefault();
                    plots['{{ $sensor['id'] }}'].zoomOut();
                });
            </script>
        </div>
    </div>

    @if(!empty($stats))
        <div class="col-md-{{ $statsBlockWidth }}">
            <div class="panel-body">
                <table class="table">
                    @foreach($stats as $label => $value)
                        <tr>
                            <th>{{ $label }}:</th>
                            <td>{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endif
</div>