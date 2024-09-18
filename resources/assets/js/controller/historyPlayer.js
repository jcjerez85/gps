function HistoryPlayer() {
	var
		_this = this,
		options = {
			index: 0,
			speed: 2000,
			marker: null,
			lastPoint: null
		};
	
	_this.setSpeed = function(speed) {
		options.speed = speed;
	};
	_this.clear = function() {
		_this.stop();
		
		if (options.marker != null) {
            app.map.removeLayer( options.marker );
			options.marker = null;
        }
	};
	_this.stop = function() {
		options.index = 0;
		options.lastPoint = null;
		
		clearTimeout( _this.playing );
	};
	_this.pause = function() {
		clearTimeout( _this.playing );
	};
	_this.play = function() {
		_this.playing = setTimeout(function () { _this.play() }, options.speed);
		_this.step();
	};

	_this.step = function() {

		if ( typeof app.history.graph.graph_data == 'undefined' ) {
			_this.stop();
			return;
		}
		if ( typeof app.history.graph.graph_data[options.index] == 'undefined' ) {
			_this.stop();
			return;
		}
				
		let
			item = app.history.graph.graph_data[options.index],
			position_id = item[3],
			position = app.history.findPosition(position_id);
		
		_this.graphSetLabel( item );
		_this.graphSetCrosshair( item[0] );

		var latlng = L.latLng( position.lat, position.lng );
		var angle = position.course;
		
		var point = app.map.project( position );
		if ( options.lastPoint !== null ) {
			angle = L.LineUtil.PolylineDecorator.computeAngle( options.lastPoint, point );
		}
		options.lastPoint = point;

		if (options.marker == null) {
			options.marker = new L.Marker(
				[0, 0],
				{
					clickable: false,
					keyboard: false,
					icon: L.divIcon({
						html: '<i class="ico ico-object-arrow" style="color:green;"></i>',
						className: 'leaf-device-marker',
						iconSize: [25, 33],
						iconAnchor: [12, 12],
						popupAnchor: [0, 0 - 33]
					})
				}
			).addTo( app.map );
		}

		options.marker.setLatLng( latlng );
		options.marker.setIconAngle( angle );
		options.marker.update();
		
		app.map.setView( latlng );
		
		options.index++;
	};
	_this.graphSetCrosshair = function (e) {
		var t = parseInt(app.history.graph.plot.pointOffset({
				x: e,
				y: 0
			}).left, 10) - app.history.graph.plot.getPlotOffset().left,
			a = app.history.graph.plot.width(),
			s = parseInt(a / 2, 10);
		t > a - s && app.history.graph.plot.pan({
			left: t - (a - s),
			top: 0
		}), s > t && app.history.graph.plot.pan({
			left: t - s,
			top: 0
		}), app.history.graph.plot.setCrosshair({
			x: e,
			y: 0
		})
	};
	_this.graphSetLabel = function ( item ) {
		$("#hoverdata").text(item[1] + ' ' + app.history.graph.unit);
		$("#hoverdata-date").text(moment.utc(item[0]).format('YYYY-MM-DD HH:mm:ss'));
	};
};

$(document).ready(function() {
	$("#graph-select").on('change', function() {
        app.history.player.clear();
    });
	$("#historySpeed").on('change', function() {
		dd( 'player.speed.changed', this.value );
		app.history.player.setSpeed( this.value );
	});
});