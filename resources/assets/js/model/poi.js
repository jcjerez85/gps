function Poi(data, map) {
    var
        _this = this,
        _map = null,
        defaults = {
            id: null,
            name: 'N/A',
            description: null,
            active: true,
            coordinates: {
                lat: null,
                lng: null,
            },
            map_icon_id: null,
            group_id: 0,
            map_icon: {
                url: null,
                width: null,
                height: null
            }
        },
        options = {},
        popup = null,
        layer = null;

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

        $( _map ).trigger('poi.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        if (options.coordinates) {
            _this.lat = options.coordinates.lat;
            _this.lng = options.coordinates.lng;
        }

        _this.updateLayer();

        $( _map ).trigger('poi.created', _this);
    };

    _this.update = function(data) {
        $( _map ).trigger('poi.update', _this);

        data = data || {};

        options = $.extend({}, options, data);

        if (options.coordinates) {
            _this.lat = options.coordinates.lat;
            _this.lng = options.coordinates.lng;
        }

        _this.updateLayer();

        $( _map ).trigger('poi.updated', _this);
    };

    _this.getLatLng = function () {
        if ( ! options.coordinates)
            return null;

        return L.latLng(options.coordinates.lat, options.coordinates.lng);
    };

    _this.isLayerVisible = function () {
        if ( options.active != true ) {
            return false;
        }

        if ( !options.coordinates )
            return false;

        if (typeof options.coordinates.lat === 'undefined')
            return false;

        if (typeof options.coordinates.lng === 'undefined')
            return false;


        return app.settings.showPoi == true;
    };

    _this.getLayer = function () {
        if (options.coordinates.lat === null || options.coordinates.lng === null)
            return false;

        var
            icon_url    = options.map_icon.url,
            width       = options.map_icon.width,
            height      = options.map_icon.height,
            position    = new L.LatLng(options.coordinates.lat, options.coordinates.lng);

        if ( ! icon_url )
            return false;

        var poiIcon = L.icon({
            iconUrl: icon_url,
            iconSize: [width, height],
            iconAnchor: [(width / 2), height],
            popupAnchor:  [0, 0 - height]
        });

        if (layer) {
            layer
                .setIcon( poiIcon )
                .setLatLng( position );

            return layer;
        }

        try {
            layer = new L.Marker( position, { icon: poiIcon });

            layer
                .on('click', _this.openPopup)
                .on('remove', _this.onlayerRemove)
                .on('add', _this.onlayerAdd);
        } catch (err) {
            console.log(err.message);
        }

        return layer;
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

    _this.openPopup = function() {

        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>Description:</th><td>'+options.description+'</td></tr>';
        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content" data-poi-id="'+options.id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title">'+options.name+'</div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        popup = L.popup({
            className: 'leaflet-popup-map-icon',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng( _this.getLatLng() )
            .setContent( html )
            .openOn( _map );

        initComponents( popup.getElement() );
    };

    _this.onlayerAdd = function(){ };

    _this.onlayerRemove = function() { };

    _this.create(data, map);
}
