function EventSys(data) {
    var
        _this = this,
        defaults = {
            id: null,
            alert_id: null,
            name: null,
            altitude: null,
            speed: null,
            course: null,
            latitude: null,
            longitude: null,
            time: null,
            device: null,
            geofence: null,
            message: null,
            detail: null,
            sound: null,
            delay: null,
        },
        options;

    _this.create = function(data) {
        $( document ).trigger('event.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        _this.lat = options.latitude;
        _this.lng = options.longitude;

        $( document ).trigger('event.created', _this);
    };

    _this.update = function(data) {

    };

    _this.sound = function() {
        if ( ! options.sound)
            return;

        var audio = new Audio(options.sound);
        audio.play();
    };

    _this.notice = function() {
        var message = options.message + '<br>';
        var notificationTitle = options.message;
        var notificationMessage = '';
        // Geofence
        if (options.geofence != null) {
            if (options.geofence.name != null) {
                message += window.lang.geofence + ': ' + options.geofence.name + '<br>';
                notificationMessage += window.lang.geofence + ': ' + options.geofence.name + ',';
            }
        }

        //Device
        if (options.device != null) {
            message += window.lang.device + ': ' + options.device.name + '<br>';
            notificationMessage += window.lang.device + ': ' + options.device.name + ', ';
        }
        if (options.speed != null) {
            message += window.lang.speed + ': ' + options.speed + ' ' + app.settings.units.speed.unit + '<br>';
            notificationMessage += window.lang.speed + ': ' + options.speed + ' ' + app.settings.units.speed.unit + ', ';
        }
        if (options.address != null) {
            message += window.lang.address + ': ' + options.address + '<br>';
            notificationMessage += window.lang.speed + ': ' + options.speed + ' ' + app.settings.units.speed.unit + ', ';
        }

        notificationMessage = notificationMessage.slice(0, notificationMessage.length - 2);

        //app.notice.info(message, window.lang.event, {timeOut: 60000});
        let delay = (typeof options.delay !== 'undefined' ? options.delay : 5) * 1000;
        app.notice.info(message, {
            backgroundColor: options.color,
            timeOut: delay,
            extendedTimeOut: delay ? 1000 : 0,
            closeButton: true,
            onclick: function () {
                app.events.select(options.id);
            }
        });

        new Notifications().notify(
            decodeHtmlEntity(notificationTitle),
            decodeHtmlEntity(notificationMessage)
        );
    };

    _this.html = function () {
        var _html = '';
        var _message = options.message + (options.geofence != null ? (' ('+options.geofence.name+')') : '');

        var $dropdown = $('<div class="btn-group dropleft droparrow"  data-position="fixed"><i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i></div>');
        var $dropdownMenu = $('<ul class="dropdown-menu"></ul>');

        $dropdownMenu.append('<li><a href="javascript:;" data-url="'+app.urls.alertEdit+'/'+options.alert_id+'" data-modal="alerts_edit"> <span class="icon event"></span><span class="text">'+window.lang.alert+'</span></a></li>');
        $dropdownMenu.append('<li><a href="javascript:;" data-url="'+app.urls.eventDoDelete+'?id='+options.id+'" data-modal="events_do_destroy"> <span class="icon delete"></span><span class="text">'+window.lang.remove+'</span></a></li>');
        $dropdown.append($dropdownMenu);

        _html += '<tr data-event-id="' + options.id + '" onClick="app.events.select(' + options.id + ');">';
        _html += '<td>';

        _html += '<div class="row">';
        _html += '<div class="col-xs-3 datetime">';
        _html += '<span class="time">'+ moment.utc(options.time).format(app.settings.timeFormat) +'</span>';
        _html += '<span class="date">'+ moment.utc(options.time).format(app.settings.dateFormat) +'</span>';
        _html += '</div>';
        _html += '<div class="col-xs-4">';
        _html += (options.device != null ? options.device.name : '-');
        _html += '</div>';
        _html += '<div class="col-xs-5">';
        _html += _message;
        _html += '</div>';
        _html += '</div>';

        if(app.settings.showEventSectionAddress) {
            _html += '<div class="row">';
            _html += '<div class="col-sm-12">';
            _html += '<span data-device="address" data-lat="'+options.latitude+'" data-lng="'+options.longitude+'"></span>';
            _html += '</div>';
            _html += '</div>';
        }

        _html += '</td>';
        _html += '<td>'+ $dropdown[0].outerHTML +'</td>';
        _html += '</tr>';

        return _html;
    };

    _this.popup = function() {
        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="'+options.latitude+'" data-lng="'+options.longitude+'"></span></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + options.latitude + ',' + options.longitude + '&cbp=12,20.09,,0,5&layer=c&hl=' + app.lang.iso + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + options.latitude + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + options.longitude + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.altitude + ':</th><td>' + options.altitude + ' ' + app.settings.units.altitude.unit + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.speed + ':</th><td>' + options.speed + ' ' + app.settings.units.speed.unit + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.time + ':</th><td>' + options.time + '</td></tr>';

        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content" data-history-id="'+options.id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title">'+options.device.name+'</div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        var popup = L.popup({
            className: 'leaflet-popup-event',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng([options.latitude, options.longitude])
            .setContent( html )
            .openOn( app.map );

        app.setView(popup.getLatLng());

        initComponents( popup.getElement() );
    };

    _this.create(data);
}

