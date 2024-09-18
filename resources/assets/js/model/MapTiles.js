function MapTiles() {
    var
        _this = this,
        map = null,
        layers = null,
        _maps = {
            1: {
                require:    'google',
                name:       'Google Normal',
                maxZoom:    20,
                traffic:    true,
                tile:       function(){

                    var options = {
                        type: 'roadmap',
                        maxZoom: 20,
                        styles: [{featureType: 'poi.business', stylers: [ { 'visibility': 'on' } ]}],
                        attribution: '<a href="https://maps.google.com/help/terms_maps/" target="_blank" ><img height="25px" src="./assets/images/google_logo.svg" /></a>'
                    };

                    var _tileLayer = L.gridLayer.googleMutant(options);

                    _tileLayer.map_id = 1;

                    return _tileLayer;
                }
            },
            2: {
                name: 'OpenStreetMap',
                maxZoom: 18,
                tile: function(){
                    var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});

                    _tileLayer.map_id = 2;

                    return _tileLayer;
                }
            },
            3: {
                require:    'google',
                name:       'Google Hybrid',
                maxZoom:    20,
                traffic:    true,
                tile:       function(){
                    var _tileLayer = L.gridLayer.googleMutant({
                        type: 'hybrid',
                        maxZoom: 20,
                        styles: [{featureType: 'poi.business', stylers: [ { 'visibility': 'on' } ]}],
                        attribution: '<a href="https://maps.google.com/help/terms_maps/" target="_blank" ><img height="25px" src="./assets/images/google_logo.svg" /></a>'
                    });

                    _tileLayer.map_id = 3;

                    return _tileLayer;
                }
            },
            4: {
                require:    'google',
                name:       'Google Satellite',
                maxZoom:    20,
                traffic:    true,
                tile:       function(){
                    var _tileLayer = L.gridLayer.googleMutant({
                        type: 'satellite',
                        maxZoom: 20,
                        styles: [{featureType: 'poi.business', stylers: [ { 'visibility': 'on' } ]}],
                        attribution: '<a href="https://maps.google.com/help/terms_maps/" target="_blank" ><img height="25px" src="./assets/images/google_logo.svg" /></a>'
                    });

                    _tileLayer.map_id = 4;

                    return _tileLayer;
                }
            },
            5: {
                require:    'google',
                name:       'Google Terrain',
                maxZoom:    20,
                traffic:    true,
                tile:       function(){
                    var _tileLayer = L.gridLayer.googleMutant({
                        type: 'terrain',
                        maxZoom: 20,
                        styles: [{featureType: 'poi.business', stylers: [ { 'visibility': 'on' } ]}],
                        attribution: '<a href="https://maps.google.com/help/terms_maps/" target="_blank" ><img height="25px" src="./assets/images/google_logo.svg" /></a>'
                    });

                    _tileLayer.map_id = 5;

                    return _tileLayer;
                }
            },
            6: {
                require:    'yandex',
                name:       'Yandex',
                maxZoom:    18,
                tile:       function(){
                    var _tileLayer = new L.Yandex(null, {maxZoom: 18, lang: app.lang.locale});

                    _tileLayer.map_id = 6;

                    return _tileLayer;
                }
            },
            7: {
                name: 'Bing Normal',
                maxZoom: 19,
                tile: function () {
                    if (!app.settings.keys.bing_maps_key)
                        return false;

                    var _tileLayer = new L.tileLayer.bing({
                        bingMapsKey: app.settings.keys.bing_maps_key,
                        imagerySet: 'Road',
                        culture: app.lang.locale,
                        maxZoom: 19
                    });

                    _tileLayer.map_id = 7;

                    return _tileLayer;
                }
            },
            8: {
                name: 'Bing Satellite',
                maxZoom: 19,
                tile: function () {
                    if (!app.settings.keys.bing_maps_key)
                        return false;

                    var _tileLayer = new L.tileLayer.bing({
                        bingMapsKey: app.settings.keys.bing_maps_key,
                        imagerySet: 'Aerial',
                        culture: app.lang.locale,
                        maxZoom: 19
                    });

                    _tileLayer.map_id = 8;

                    return _tileLayer;
                }
            },
            9: {
                name: 'Bing Hybrid',
                maxZoom: 19,
                tile: function () {
                    if (!app.settings.keys.bing_maps_key)
                        return false;

                    var _tileLayer = new L.tileLayer.bing({
                        bingMapsKey: app.settings.keys.bing_maps_key,
                        imagerySet: 'AerialWithLabels',
                        culture: app.lang.locale,
                        maxZoom: 19
                    });

                    _tileLayer.map_id = 9;

                    return _tileLayer;
                }
            },
            10: {
                name: 'Here Normal',
                maxZoom: 18,
                tile: function () {
                    var tilesLink = '';

                    if (app.settings.keys.here_api_key) {
                      tilesLink = 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}&lg={language}';
                    } else if (app.settings.keys.here_map_id && app.settings.keys.here_map_code) {
                      tilesLink = 'https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?app_id={app_id}&app_code={app_code}&lg={language}';
                    } else {
                        return false;
                    }

                    var _tileLayer = L.tileLayer(tilesLink, {
                        attribution: 'Map &copy; 2016 <a href="http://developer.here.com">HERE</a>',
                        subdomains: '1234',
                        base: 'base',
                        type: 'maptile',
                        scheme: 'normal.day',
                        apiKey: app.settings.keys.here_api_key,
                        app_id: app.settings.keys.here_map_id,
                        app_code: app.settings.keys.here_map_code,
                        mapID: 'newest',
                        maxZoom: 18,
                        language: app.lang.iso3,
                        format: 'png8',
                        size: '256'
                    });

                    _tileLayer.map_id = 10;

                    return _tileLayer;
                }
            },
            11: {
                name: 'Here Sattelite',
                maxZoom: 18,
                tile: function () {
                    var tilesLink = '';

                    if (app.settings.keys.here_api_key) {
                      tilesLink = 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}&lg={language}';
                    } else if (app.settings.keys.here_map_id && app.settings.keys.here_map_code) {
                      tilesLink = 'https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?app_id={app_id}&app_code={app_code}&lg={language}';
                    } else {
                        return false;
                    }

                    var _tileLayer = L.tileLayer(tilesLink, {
                        attribution: 'Map &copy; 2016 <a href="http://developer.here.com">HERE</a>',
                        subdomains: '1234',
                        base: 'aerial',
                        type: 'maptile',
                        scheme: 'satellite.day',
                        apiKey: app.settings.keys.here_api_key,
                        app_id: app.settings.keys.here_map_id,
                        app_code: app.settings.keys.here_map_code,
                        mapID: 'newest',
                        maxZoom: 18,
                        language: app.lang.iso3,
                        format: 'png8',
                        size: '256'
                    });

                    _tileLayer.map_id = 11;

                    return _tileLayer;
                }
            },
            12: {
                name: 'Here Hybrid',
                maxZoom: 18,
                tile: function () {
                    var tilesLink = '';

                    if (app.settings.keys.here_api_key) {
                      tilesLink = 'https://{s}.{base}.maps.ls.hereapi.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?apiKey={apiKey}&lg={language}';
                    } else if (app.settings.keys.here_map_id && app.settings.keys.here_map_code) {
                      tilesLink = 'https://{s}.{base}.maps.cit.api.here.com/maptile/2.1/{type}/{mapID}/{scheme}/{z}/{x}/{y}/{size}/{format}?app_id={app_id}&app_code={app_code}&lg={language}';
                    } else {
                        return false;
                    }

                    var _tileLayer = L.tileLayer(tilesLink, {
                        attribution: 'Map &copy; 2016 <a href="http://developer.here.com">HERE</a>',
                        subdomains: '1234',
                        base: 'aerial',
                        type: 'maptile',
                        scheme: 'hybrid.day',
                        apiKey: app.settings.keys.here_api_key,
                        app_id: app.settings.keys.here_map_id,
                        app_code: app.settings.keys.here_map_code,
                        mapID: 'newest',
                        maxZoom: 18,
                        language: app.lang.iso3,
                        format: 'png8',
                        size: '256'
                    });

                    _tileLayer.map_id = 12;

                    return _tileLayer;
                }
            },
            14: {
                name: 'MapBox Normal',
                maxZoom: 18,
                tile: function () {
                    if (!app.settings.keys.mapbox_access_token)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                        attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a>',
                        tileSize: 512,
                        maxZoom: 18,
                        zoomOffset: -1,
                        id: 'streets-v11',
                        accessToken: app.settings.keys.mapbox_access_token,
                    });

                    _tileLayer.map_id = 14;

                    return _tileLayer;
                }
            },
            15: {
                name: 'MapBox Satellite',
                maxZoom: 18,
                tile: function () {
                    if (!app.settings.keys.mapbox_access_token)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                        attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a>',
                        tileSize: 512,
                        maxZoom: 18,
                        zoomOffset: -1,
                        id: 'satellite-v9',
                        accessToken: app.settings.keys.mapbox_access_token,
                    });

                    _tileLayer.map_id = 15;

                    return _tileLayer;
                }
            },
            16: {
                name: 'MapBox Hybrid',
                maxZoom: 18,
                tile: function () {
                    if (!app.settings.keys.mapbox_access_token)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
                        attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a>',
                        tileSize: 512,
                        maxZoom: 18,
                        zoomOffset: -1,
                        id: 'satellite-streets-v11',
                        accessToken: app.settings.keys.mapbox_access_token,
                    });

                    _tileLayer.map_id = 16;

                    return _tileLayer;
                }
            },
            17: {
                name: 'MapTiler Basic',
                maxZoom: 20,
                tile: function () {
                    if (!app.settings.keys.maptiler_key)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.maptiler.com/maps/{scheme}/{z}/{x}/{y}.png?key={key}', {
                        scheme: 'basic',
                        key: app.settings.keys.maptiler_key,
                        tileSize: 512,
                        zoomOffset: -1,
                        minZoom: 1,
                        maxZoom: 20,
                        attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">© OpenStreetMap contributors</a>',
                        crossOrigin: true
                    });

                    _tileLayer.map_id = 17;

                    return _tileLayer;
                }
            },
            18: {
                name: 'MapTiler Streets',
                maxZoom: 20,
                tile: function () {
                    if (!app.settings.keys.maptiler_key)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.maptiler.com/maps/{scheme}/{z}/{x}/{y}.png?key={key}', {
                        scheme: 'streets',
                        key: app.settings.keys.maptiler_key,
                        tileSize: 512,
                        zoomOffset: -1,
                        minZoom: 1,
                        maxZoom: 20,
                        attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">© OpenStreetMap contributors</a>',
                        crossOrigin: true
                    });

                    _tileLayer.map_id = 18;

                    return _tileLayer;
                }
            },
            19: {
                name: 'MapTiler Satellite',
                maxZoom: 20,
                tile: function () {
                    if (!app.settings.keys.maptiler_key)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.maptiler.com/maps/{scheme}/{z}/{x}/{y}.jpg?key={key}', {
                        scheme: 'hybrid',
                        key: app.settings.keys.maptiler_key,
                        tileSize: 512,
                        zoomOffset: -1,
                        minZoom: 1,
                        maxZoom: 20,
                        attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">© MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">© OpenStreetMap contributors</a>',
                        crossOrigin: true
                    });

                    _tileLayer.map_id = 19;

                    return _tileLayer;
                }
            },
            21: {
                name: 'OpenMapTiles OSM',
                maxZoom: 20,
                tile: function () {
                    if (!app.settings.openmaptiles_url)
                        return false;

                    var _tileLayer = L.tileLayer(app.settings.openmaptiles_url + '/styles/{scheme}/{z}/{x}/{y}.png', {
                        scheme: 'osm-bright',
                        minZoom: 1,
                        maxZoom: 20,
                        crossOrigin: true
                    });

                    _tileLayer.map_id = 21;

                    return _tileLayer;
                }
            },
            22: {
              name: 'OpenRailway Infrastructure',
              maxZoom: 18,
              tile: function () {
                var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});
                _tileLayer.map_id = 22;

                _tileLayer.overlayLayer = L.tileLayer('https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png', {
                    attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors</a>, Style: <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA 2.0</a> <a href="http://www.openrailwaymap.org/">OpenRailwayMap</a> and OpenStreetMap',
                    subdomains: 'abc',
                    maxZoom: 18,
                });

                return _tileLayer;
              }
            },
            23: {
              name: 'OpenRailway Max Speeds',
              maxZoom: 18,
              tile: function () {
                var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});
                _tileLayer.map_id = 23;

                _tileLayer.overlayLayer = L.tileLayer('https://{s}.tiles.openrailwaymap.org/maxspeed/{z}/{x}/{y}.png', {
                  attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors</a>, Style: <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA 2.0</a> <a href="http://www.openrailwaymap.org/">OpenRailwayMap</a> and OpenStreetMap',
                  subdomains: 'abc',
                  maxZoom: 18,
                });

                return _tileLayer;
              }
            },
            24: {
              name: 'OpenRailway Signaling',
              maxZoom: 18,
              tile: function () {
                  var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});
                  _tileLayer.map_id = 24;

                  _tileLayer.overlayLayer = L.tileLayer('https://{s}.tiles.openrailwaymap.org/signals/{z}/{x}/{y}.png', {
                    attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors</a>, Style: <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA 2.0</a> <a href="http://www.openrailwaymap.org/">OpenRailwayMap</a> and OpenStreetMap',
                    subdomains: 'abc',
                    maxZoom: 18,
                  });

                return _tileLayer;
              }
            },
            25: {
              name: 'OpenRailway Electrification',
              maxZoom: 18,
              tile: function () {
                  var _tileLayer =  new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom: 18});
                  _tileLayer.map_id = 25;

                  _tileLayer.overlayLayer = L.tileLayer('https://{s}.tiles.openrailwaymap.org/electrified/{z}/{x}/{y}.png', {
                    attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors</a>, Style: <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA 2.0</a> <a href="http://www.openrailwaymap.org/">OpenRailwayMap</a> and OpenStreetMap',
                    subdomains: 'abc',
                    maxZoom: 18,
                  });

                return _tileLayer;
              }
            },
            26: {
                name: 'TomTom Basic',
                maxZoom: 20,
                tile: function () {
                    if (!app.settings.keys.tomtom_key)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.tomtom.com/map/1/tile/{scheme}/main/{z}/{x}/{y}.png?key={key}', {
                        scheme: 'basic',
                        key: app.settings.keys.tomtom_key,
                        tileSize: 256,
                        minZoom: 1,
                        maxZoom: 20,
                        attribution: '<a href="https://www.tomtom.com" target="_blank">© TomTom</a>',
                    });

                    _tileLayer.map_id = 26;

                    return _tileLayer;
                }
            },
            27: {
                name: 'TomTom Satellite',
                maxZoom: 19,
                tile: function () {
                    if (!app.settings.keys.tomtom_key)
                        return false;

                    var _tileLayer = L.tileLayer('https://api.tomtom.com/map/1/tile/{scheme}/main/{z}/{x}/{y}.jpg?key={key}', {
                        scheme: 'sat',
                        key: app.settings.keys.tomtom_key,
                        tileSize: 256,
                        minZoom: 1,
                        maxZoom: 19,
                        attribution: '<a href="https://www.tomtom.com" target="_blank">© TomTom</a>',
                    });

                    _tileLayer.map_id = 27;

                    return _tileLayer;
                }
            },

            51: {
                name: 'Google Normal B',
                maxZoom: 21,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://mt0.google.com/vt/lyrs=m&hl=' + app.lang.locale + '&x={x}&y={y}&z={z}&s=Ga&gl=' + app.lang.locale, {
                        maxZoom: 21,
                        minZoom: 0,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 51;

                    return _tileLayer;
                }
            },
            53: {
                name: 'Google Hybrid B',
                maxZoom: 21,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://mt0.google.com/vt/lyrs=y&hl=' + app.lang.locale + '&x={x}&y={y}&z={z}&s=Ga&gl=' + app.lang.locale, {
                        maxZoom: 21,
                        minZoom: 0,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 53;

                    return _tileLayer;
                }
            },

            54: {
                name: 'Google Satellite B',
                maxZoom: 21,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://mt0.google.com/vt/lyrs=s&hl=' + app.lang.locale + '&x={x}&y={y}&z={z}&s=Ga&gl=' + app.lang.locale, {
                        maxZoom: 21,
                        minZoom: 0,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 54;

                    return _tileLayer;
                }
            },

            55: {
                name: 'Google Terrain B',
                maxZoom: 21,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://mt0.google.com/vt/lyrs=p&hl=' + app.lang.locale + '&x={x}&y={y}&z={z}&s=Ga&gl=' + app.lang.locale, {
                        maxZoom: 21,
                        minZoom: 0,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 55;

                    return _tileLayer;
                }
            },

            70: {
                name: 'Carto',
                maxZoom: 18,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}@2x.png', {
                        maxZoom: 18,
                        minZoom: 2,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 70;

                    return _tileLayer;
                }
            },
            
            71: {
                name: 'LocationIQ Streets',
                maxZoom: 18,
                tile: function() {
                    var _tileLayer = new L.TileLayer('https://{s}-tiles.locationiq.com/v3/streets/r/{z}/{x}/{y}.png?key=pk.0f147952a41c555a5b70614039fd148b', {
                        maxZoom: 18,
                        minZoom: 2,
                        attribution: '<a href="https://seelight.site" target="_system">Seelight</a>'
                    });

                    _tileLayer.map_id = 71;

                    return _tileLayer;
                }
            },
            98: {
                name: 'One Map Singapure',
                maxZoom: 16,
                tile: function () {
                    var _tileLayer = new L.TileLayer('http://maps-a.onemap.sg/v2/Default/{z}/{x}/{y}.png', {
                        minZoom: 1,
                        maxZoom: 16,
                        detectRetina: true,
                        attribution: ""
                    });

                    _tileLayer._tileOnError = function (done, tile, e) {

                        var errorUrl = tile.src.replace('maps-a.onemap.sg/v2/Default', 'tile.openstreetmap.org');

                        if (errorUrl && tile.src !== errorUrl) {
                            tile.src = errorUrl;
                        }

                        done(e, tile);
                    };

                    _tileLayer.map_id = 98;

                    return _tileLayer;
                }
            },
            99: {
                name: 'Tourist map Slovakia',
                maxZoom: 16,
                tile: function () {
                    var _tileLayer = new L.TileLayer('https://{s}.freemap.sk/T/{z}/{x}/{y}.png', {
                        minZoom: 1,
                        maxZoom: 16,
                        subdomains: ['a', 'b', 'c'],
                        attribution: "Copyright <a href='http://www.freemap.sk'>©2016 Freemap Slovakia</a>. <a href='http://www.openstreetmap.org'>OpenStreetMap</a>, Licensed as Creative Commons <a href='http://creativecommons.org/licenses/by-sa/2.0'>CC-BY-SA 2.0</a>. Some rights reserved."
                    });

                    _tileLayer._tileOnError = function (done, tile, e) {

                        var errorUrl = tile.src.replace('freemap.sk/T', 'tile.openstreetmap.org');

                        if (errorUrl && tile.src !== errorUrl) {
                            tile.src = errorUrl;
                        }

                        done(e, tile);
                    };

                    _tileLayer.map_id = 99;

                    return _tileLayer;
                }
            }
        };

    _this.mapList = function () {
        var requires = {};

        _this.layers = {};

        $.each(app.settings.availableMaps, function (index, map_id) {
            if (! (map_id in _maps))
                return;

            var _tile = _maps[map_id];

            if (!_tile.tile())
                return;

            var tileLayer = _tile.tile();
            _this.addOverlayLayerEvents(tileLayer);
            _this.layers[_tile.name] = tileLayer;

            if (typeof _tile.require !== "undefined" && typeof requires[_tile.require] === "undefined") {
                requires[_tile.require] = _tile.require;
            }
        });

        $.each(requires, function (index, require) {
            switch (require) {
                case 'google':

                    var _googleUrl = 'https://maps.google.com/maps/api/js?v=weekly&callback=googleMapLoaded&' + decodeURIComponent( $.param( app.settings.googleQueryParam ) );

                    if ($('script').filter(function () { return ($(this).attr('src') === _googleUrl);}).length === 0) {
                        var scriptTag = document.createElement('script');
                        scriptTag.type = 'text/javascript';
                        scriptTag.async = true;
                        scriptTag.src = _googleUrl;
                        document.head.appendChild(scriptTag);
                    }
                    break;
                case 'yandex':
                    break;
            }
        });
    };

    _this.initControls = function() {
        var layersControl = L.control.layers(_this.layers, {}, {
            collapsed: true
        });

        layersControl.addTo(_this.map);

        $(layersControl.getContainer()).remove();

        $(layersControl.onAdd(_this.map)).insertAfter($('#map-controls > div').first());
    };

    _this.current = function () {
        if (typeof _maps[app.settings.map_id] === 'undefined')
            app.settings.map_id = null;

        if (app.settings.map_id) {
            var _existMapID = false;

            $.each(app.settings.availableMaps, function (index, map_id) {
                //continue
                if (app.settings.map_id != map_id)
                    return;

                if (!_maps[map_id].tile())
                    return;

                _existMapID = true;
                return false;
            });

            if (!_existMapID) {
                app.settings.map_id = null;
            }
        }

        if (!app.settings.map_id) {
            $.each(app.settings.availableMaps, function (index, map_id) {
                if (!_maps[map_id].tile())
                    return;

                app.settings.map_id = map_id;
                return false;
            });
        }

        return _this.layers[_maps[app.settings.map_id].name];
    };

    _this.addLayerCurrent = function () {
        var tileLayer = _this.current();

        _this.map.addLayer(tileLayer);

        if ( tileLayer.options.maxZoom ) {
            _this.map.options.maxZoom = tileLayer.options.maxZoom;

            if (_this.map.getZoom() > tileLayer.options.maxZoom ) {
                _this.map.setZoom(tileLayer.options.maxZoom);
            }
        }
    };

    _this.disableTraffic = function () {
        dd('disabling traffic');

        $('#showTraffic')
            .attr('disabled', 'disabled')
            .parent().addClass('disabled');
    };
    _this.enableTraffic = function () {
        dd('enabling traffic');

        $('#showTraffic')
            .removeAttr('disabled')
            .parent().removeClass('disabled');
    };

    _this.addOverlayLayerEvents = function (_tileLayer) {
      if (! _tileLayer.overlayLayer) {
        return;
      }

      _tileLayer.on('add', function (layer) {
        if (_this.map.hasLayer(layer.target.overlayLayer)) {
          return;
        }

        layer.target.overlayLayer.setZIndex(layer.target.zIndex)

        _this.map.addLayer(layer.target.overlayLayer);
      }).on('remove', function (layer) {
        if (! _this.map.hasLayer(layer.target.overlayLayer)) {
          return;
        }

        _this.map.removeLayer(layer.target.overlayLayer);
      });
    };

    _this.init = function (map) {
        dd('mapTiles.init');

        _this.map = map;

        _this.mapList();

        _this.addLayerCurrent();

        _this.map.on('baselayerchange', function (e) {
            dd('baselayerchange', e);

            var
                map_id = e.layer.map_id,
                _map = _maps[map_id];

            _this.map.options.maxZoom = _map.maxZoom;

            if ( _this.map.getZoom() > _this.map.options.maxZoom )
                _this.map.setZoom(_this.map.options.maxZoom);

            if (_map.traffic) {
                _this.enableTraffic();
            } else {
                _this.disableTraffic();
            }

            app.changeSetting('map_id', map_id);
            app.saveSetting('map', _map.name);
        });

    };
}

function googleMapLoaded() {}