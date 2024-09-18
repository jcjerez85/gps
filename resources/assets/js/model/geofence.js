function Geofence(data, map) {
    var
        _this = this,
        _map = null,
        defaults = {
            id: null,
            name: 'N/A',
            active: true,
            polygon_color: '#dddddd',
            coordinates: [],
            center: {},
            radius: null,
            type: 'polygon',
            device_id: null,
            group_id: null,
        },
        options = {},
        layer = null,
        popup = null;

    _this.id = function() {
        return options.id;
    };

    _this.options = function() {
        return options;
    };

    _this.active = function(value) {
        options.active = value;

        _this.update();
    };

    _this.isVisible = function() {
        return options.active == true;
    };

    _this.create = function(data, map) {
        _map = map;

        $( _map ).trigger('geofence.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        $( _map ).trigger('geofence.created', _this);
    };

    _this.update = function(data) {
        $( _map ).trigger('geofence.update', _this);

        data = data || {};

        options = $.extend({}, options, data);

        $( _map ).trigger('geofence.updated', _this);
    };

    _this.getLatLngs = function() {
        if ( ! layer )
            return null;

        if (layer instanceof L.Polygon) {
            return layer.getLatLngs()[0];
        }
        else if (layer instanceof L.Circle) {
            return layer.getLatLng();
        }
    };

    _this.getLatLng = function() {
        if ( ! layer )
            return null;

        if (layer instanceof L.Polygon) {
            return layer.getLatLngs()[0][0];
        }
        else if (layer instanceof L.Circle) {
            return layer.getLatLng();
        }
    };


    _this.getBounds = function() {
        if ( ! layer )
            return [];

        return layer.getBounds();
    };

    _this.isLayerVisible = function () {
        if ( options.active != true ) {
            return false;
        }

        return app.settings.showGeofences == true;
    };

    _this.setLayer = function (_layer) {
        _this.removeLayer();

        layer = _layer;

        layer
            .on('remove', _this.onLayerRemove)
            .on('add', _this.onLayerAdd);

        return layer;
    };

    _this.getLayer = function (_options) {
        _options = $.extend({}, options, _options);

        if (layer)
            return layer;

        try {
            switch (_options.type) {
                case 'circle':
                    layer = L.circle(_options.center, {
                        radius: _options.radius,
                        color: _options.polygon_color,
                        weight: 3,
                        opacity: 1,
                        fill: true,
                        fillOpacity: 0.3,
                        fillColor: _options.polygon_color
                    });
                    break;

                case 'polygon':
                default:
                    layer = L.polygon(_options.coordinates, {
                        color: _options.polygon_color,
                        weight: 3,
                        opacity: 1,
                        fill: true,
                        fillOpacity: 0.3,
                        fillColor: _options.polygon_color
                    });
            }

            layer
                .on('remove', _this.onLayerRemove)
                .on('add', _this.onLayerAdd)
                .on('mouseover', _this.onLayerMouseOver)
                .on('mouseout', _this.onLayerMouseOut);

        } catch (err) {
            console.log(err.message);
        }

        return layer;
    };

    _this.onLayerMouseOver = function() {
        if (!popup)
            return;

        L.DomUtil.addClass(popup._icon, 'leaflet-hover');
    };

    _this.onLayerMouseOut = function() {
        if (!popup)
            return;

        L.DomUtil.removeClass(popup._icon, 'leaflet-hover');
    };

    _this.updateLayer = function() {
        if ( ! _this.isLayerVisible() ) {
            _this.removeLayer();
        }
    };

    _this.removeLayer = function() {
        if ( ! layer )
            return;

        _map.removeLayer( layer );

        layer = null;
    };

    _this.enableEdit = function(_options) {
        _this.getLayer(_options).editing.enable();
    };

    _this.disableEdit = function() {
        _this.getLayer().editing.disable();
    };

    _this.addPopup = function() {
        if ( ! layer )
            return;

        if ( ! app.settings.showGeofences )
            return;


        var size = app.settings.showGeofenceSize ? _this.formatArea( _this.getArea() ) : null;

        popup = new L.Marker(
            null,
            {
                icon: L.divIcon({
                    html: '<div class="name" style="background-color: ' + convertHex(options.polygon_color, 81) + '">' + options.name + (size ? ' ('+size+')' : '') + '</div>',
                    className: 'leaflet-popup-geofence',
                    iconSize: 'auto'
                    //iconAnchor: [(width / 2), (height / 2)],
                    //popupAnchor: [0, 0 - height]
                })
            }
        );

        var _latLng = _this.getLatLng();

        if ( ! _latLng)
            return;

        popup.setLatLng(_latLng).addTo(_map);
    };

    _this.removePopup = function() {
        if ( popup )
            popup.remove();
    };

    _this.onLayerAdd = function() {
        dd('goefence.onLayerAdd');
        _this.addPopup();
    };

    _this.onLayerRemove = function() {
        dd('goefence.onLayerRemove');
        _this.removePopup();
    };

    _this.polygonToCircle = function() {
        if ( ! options.coordinates)
            return;

        if ( ! options.coordinates.length > 0)
            return;

        var _layer = L.polygon(options.coordinates);

        _map.addLayer( _layer );

        if ( ! _layer.getBounds().isValid())
            return;

        var
            center = _layer.getBounds().getCenter(),
            radius = _layer.getBounds().getSouthWest().distanceTo(_layer.getBounds().getNorthEast()) / 2;

        _map.removeLayer( _layer );
        _layer = null;

        return {
            center: center,
            radius: radius
        }
    };

    _this.circleToPolygon = function() {
        if ( ! options.center)
            return;

        if ( ! options.radius)
            return;

        var _layer = L.circle(options.center, {radius: options.radius});

        _map.addLayer( _layer );

        var
            coordinates = _layer.toPolygon();

        _map.removeLayer( _layer );
        _layer = null;

        return {
            coordinates: coordinates
        }
    };

    _this.getArea = function() {
        if ( ! _this.getLayer())
            return null;


        if (_this.getLayer() instanceof L.Polygon)
            return L.GeometryUtil.geodesicArea(_this.getLayer().getLatLngs()[0]);

        if (_this.getLayer() instanceof L.Circle)
            return Math.pow(_this.getLayer().getRadius(), 2) * Math.PI;

        return null;
    };

    _this.formatArea = function(area) {
        area = area * 0.000001 * app.settings.units.distance.radio;

        return area.toFixed(4) + app.settings.units.distance.unit + '&sup2;';
    };


    _this.create(data, map);
}