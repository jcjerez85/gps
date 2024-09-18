function HistoryGraph() {
    var
        _this = this;
    
    _this.init = function() {
        _this.plot =  null;
        _this.unit = null;
        _this.graph_data = [];
    };

    _this.options = function() {
        return {
            colors: ["rgba(76, 84, 84, 1)"],
            series: {
                shadowSize: 0
            },
            crosshair: {
                mode: "x"
            },
            lines: {
                show: true,
                lineWidth: 1.5,
                lineColor: 'red',
                fill: true,
                fillColor: "rgba(76, 84, 84, 0.4)"
            },
            zoom: {
                interactive: false
            },
            pan: {
                interactive: true
            },
            xaxis: {
                minTickSize: [30, "minute"],
                mode: 'time',
                twelveHourClock: false,
            },
            yaxis: {
                tickFormatter: function (value, axis) {
                    return value.toFixed(axis.tickDecimals) + _this.unit;
                },
                minTickSize: 1,
                tickDecimals: 0,
                zoomRange: false
            },

            legend: {
                noColumns: 0,
                labelFormatter: function (label, series) {
                    return "<font color=\"white\">" + label + "</font>";
                },
                backgroundColor: "#000",
                backgroundOpacity: 1.0,
                labelBoxBorderColor: "#000000",
                position: "nw"
            },
            grid: {
                color: "#999999",
                margin: {
                    top: 10,
                    left: 10,
                    bottom: 10,
                    right: 10
                },
                hoverable: true,
                clickable: true,
                borderWidth: 1,
                borderColor: '#DDDDDD'
            }
        };
    };

    _this.events = function() {
        $(document).on('show.bs.tab', '#graph_sensors [data-toggle="tab"]', function() {
            _this.loadData($(this).attr('data-id'));
        });
    };

    _this.events();

    _this.clear = function() {
        $('#bottom-history').hide();

        if (_this.plot && typeof _this.plot.shutdown !== 'undefined') {
            _this.plot.shutdown();
        }
        $("#placeholder").unbind("plotclick");
        $("#placeholder").unbind("plothover");

        _this.graph_data = [];
        _this.plot = {};

        $('#hoverdata').html('');
        $('#hoverdata-date').html('');

        app.map.invalidateSize();
    };

    _this.loadData = function(type) {
        var sensor = null;

        _this.init();

        $.each(window.history_sensors, function(index, item) {
            if (item.key == type)
                sensor = item;
        });

        if (!sensor)
            return;

        $.each(app.history.positions, function(index, position) {
            var tdate = position.t;
            var y = tdate.substr(0, 10);
            var h = tdate.substr(11, 8);
            var date = new Date(y + 'T' + h + 'Z');
            _this.graph_data.push([date.getTime(), position[sensor.key], 0, position.id]);
        });

        _this.unit = sensor.unit;
        _this.plot = $.plot("#placeholder", [{
            data: _this.graph_data,
            color: "#999999",
            lines: {
                fill: true,
                lineWidth: 1
            },
        }], _this.options());

        $("#placeholder").bind("plothover", function (event, pos, item) {
            if (item != null) {
                $("#hoverdata").text( item.datapoint[1] + " " + sensor.unit );
                $("#hoverdata-date").text( moment.utc(item.datapoint[0]).format('YYYY-MM-DD HH:mm:ss') );
            }
        });

        $("#placeholder").bind("plotclick", function (event, pos, pitem) {
            if (pitem != null) {
                var
                    position_id = pitem.series.data[pitem.dataIndex][3],
                    position_start_id = pitem.series.data[0][3],
                    position = app.history.findPosition(position_id),
                    position_start = app.history.findPosition(position_start_id);

                app.history.historyPointPopup(position, true, position_start);
            }
        });
    }

    _this.graphLeft = function(e) {
        if (e)
            e.preventDefault();

        _this.plot.pan({
            left: -100
        });
    };

    _this.graphRight = function(e) {
        if (e)
            e.preventDefault();

        _this.plot.pan({
            left: +100
        });
    };

    _this.zoomIn = function(e) {
        if (e)
            e.preventDefault();

        _this.plot.zoom({ center: { left: 10, top: 0 } });
    };

    _this.zoomOut = function(e) {
        if (e)
            e.preventDefault();

        _this.plot.zoomOut({ center: { left: 10, top: 0 } });
    };
}
