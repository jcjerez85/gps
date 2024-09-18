function dd() {
    if ( ! app.debug )
        return;

    console.log( arguments );
}

var elementContainerView = function($container, $element) {
    if ( ! $element.length)
        return false;

    var hT = $element.offset().top,
        wT = $container.offset().top,
        wH = $container.height();

    return (hT - wT) <= (wT + wH);
}

var decodeHtmlEntity = function(str) {
    return str.replace(/&#(\d+);/g, function(match, dec) {
        return String.fromCharCode(dec);
    });
};

var encodeHtmlEntity = function(str) {
    var buf = [];
    for (var i=str.length-1;i>=0;i--) {
        buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
    }
    return buf.join('');
};

sidebarSearch = function( value, items, select, container ) {
    dd('sidebarSearch');

    var _items,
        $list = $(container);
    dd('sidebarSearch.hide.complete');

    if (value !== '')
    {
        $('.group .group-heading', $list).hide();

        $( 'li[' + select +']' , $list).addClass('hidden');
        $( 'tr[' + select +']' , $list).addClass('hidden');

        _items = items.filter( function(item) {
            return (item.searchValue.indexOf( value ) >= 0);
        });

        $.each( _items, function(index, item) {
            $( 'li[' + select +'="'+ item.id() + '"]' , $list).removeClass('hidden');
            $( 'tr[' + select +'="'+ item.id() + '"]' , $list).removeClass('hidden');
        });

    } else {
        $( 'li[' + select +']' , $list).removeClass('hidden');
        $( 'tr[' + select +']' , $list).removeClass('hidden');
    }

    $('.group', $list).each(function(){
        var $group = $(this);

        if ( $('.group-list li[' + select +']:not(.hidden)', $group).length )
        {
            $('.group-heading', $group).show();

            $('.collapse', $group).addClass('in');
            $('.group-title', $group).removeClass('collapsed');
        }
    });

    if (value === '') {
        $('.group-title[aria-expanded="false"]', $list).addClass('collapsed');
        $('.collapse[aria-expanded="false"]', $list).removeClass('in');
    }
};

function handlerFail(jqXHR, textStatus, errorThrown) {
    dd(jqXHR, textStatus, errorThrown);

    if (jqXHR.status == 401) {
        return window.location.reload();
    }

    if (jqXHR.status == 503) {
        return window.location.reload();
    }

    if (jqXHR.status == 418) {
        var modal = $modal.initModal('confirmed-action');
        var _text;

        try {
            _text = JSON.parse(jqXHR.responseText);
            _text = _text.view;
        } catch (e) {
            _text = jqXHR.responseText
        }

        modal.find('.contents').html(_text);
        modal.modal('show');

        initComponents( modal );

        $modal.initEvent(modal, 'content-success');
    }
}

function handlerFailTarget(jqXHR, textStatus, errorThrown, target)
{
    handlerFail(jqXHR, textStatus, errorThrown);

    var _text = jqXHR.responseText;

    if (jqXHR.status == 418) {
        try {
            _text = JSON.parse(jqXHR.responseText);
            _text = _text.view;
        } catch (e) {
            _text = jqXHR.responseText
        }
    }

    var _dom = $(_text);

    if (_dom.find('.modal-body').length)
        _text = _dom.find('.modal-body').html();

    if (jqXHR.status != 418) {
        target.html(_text);
    }
}

function handlerFailModal(jqXHR, textStatus, errorThrown)
{
    handlerFail(jqXHR, textStatus, errorThrown);

    modal = $modal.initModal('error-modal');
    modal.find('.contents').html( jqXHR.responseText );
/*
    if (typeof jqXHR.responseJSON.errors) {
        var text = '';

        $.each( jqXHR.responseJSON.errors, function( key, value ) {
            text = text + value + '<br>';
        });

        console.log(text, jqXHR.responseJSON.errors);
        modal.find('.contents').html(text );
    }
*/
    modal.modal('show');

    initComponents( modal );
}

function handlerFailNotice(jqXHR, textStatus, errorThrown)
{
    handlerFail(jqXHR, textStatus, errorThrown);

    if ( ! typeof jqXHR.responseJSON.errors)
        return;

    $.each( jqXHR.responseJSON.errors, function( key, value ) {
        app.notice.error(value);
    });
}

function fetchFromObject(obj, prop) {

    if(typeof obj === 'undefined') {
        return false;
    }

    var _index = prop.indexOf('.');

    if(_index > -1) {
        return fetchFromObject(obj[prop.substring(0, _index)], prop.substr(_index + 1));
    }

    return obj[prop];
}

function dialogWindow(event, title, containerID) {
    event.preventDefault();

    containerID = containerID || 'dialog-window-' + Date.now();

    var url = $(event.target).attr('href');

    if ( $('#'+containerID).length )
        return;

    $('body').append('<div id="' + containerID + '"><iframe src="' + url + '" style="border: 0; width: 100%; height: 100%;"></iframe></div>');

    $('#'+containerID).dialog({
        autoOpen: false,
        height: 364,
        width: 500,
        resizable:true,
        draggable:true,
        title: title,
        close: function (event, ui) { $(this).remove(); },
        open: function(){
            var parent = $('div[aria-describedby="' + containerID + '"]');
            var closeBtn = parent.find('.ui-dialog-titlebar-close');
            closeBtn.html('<span>×</span>');
        }
    }).dialog('open');

    dialogMoveToTop( $container.parent('.ui-dialog.ui-widget.ui-widget-content'), true);
}

function dialogMoveToTop(el, check) {
    var elz = parseInt(el.css('z-index'));
    var index = parseInt(el.css('z-index'));
    $.each($('.ui-dialog.ui-widget.ui-widget-content'), function( key, value ) {
        if (index < parseInt($(this).css('z-index'))) {
            index = parseInt($(this).css('z-index'));
        }
    });

    if ((elz < index && typeof check === 'undefined') || typeof check !== 'undefined' ) {
        el.css('z-index', index + 2);
    }
}

function convertHex(hex,opacity){
    hex = hex.replace('#','');
    r = parseInt(hex.substring(0,2), 16);
    g = parseInt(hex.substring(2,4), 16);
    b = parseInt(hex.substring(4,6), 16);
    result = 'rgba('+r+','+g+','+b+','+opacity/100+')';

    return result;
}

function degToLatLng(deg) {
    var arr = deg.split(':');

    var d = parseFloat(arr[0]);
    var m = parseInt(arr[1]);
    var s = parseFloat(arr[2]);

    var v = (Math.abs(d) + (m / 60) + (s / 3600));
    if (d < 0.0) v = 0.0 - v;

    return v.toFixed(6);
}

function placesRouteLatLngsToPointsString(a) {
    if (a.length > 0) {
        var d = [];
        for (var c = 0; c < a.length; c++) {
            var f = a[c];
            var e = f.lat;
            var b = f.lng;
            d.push({lat: parseFloat(e).toFixed(6), lng: parseFloat(b).toFixed(6)});
        }
        return JSON.stringify(d);
    }

    return '';
}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

var DateDiff = {

    inDays: function(d1, d2) {
        var t2 = d2.getTime();
        var t1 = d1.getTime();

        return parseInt((t2-t1)/(24*3600*1000));
    },

    inWeeks: function(d1, d2) {
        var t2 = d2.getTime();
        var t1 = d1.getTime();

        return parseInt((t2-t1)/(24*3600*1000*7));
    },

    inMonths: function(d1, d2) {
        var d1Y = d1.getFullYear();
        var d2Y = d2.getFullYear();
        var d1M = d1.getMonth();
        var d2M = d2.getMonth();

        return (d2M+12*d2Y)-(d1M+12*d1Y);
    },

    inYears: function(d1, d2) {
        return d2.getFullYear()-d1.getFullYear();
    }
};

function momentCalendar(val, parent) {
    var date_from = $(parent).find('input[name="date_from"]');
    var date_to = $(parent).find('input[name="date_to"]');
    var format = 'YYYY-MM-DD';
    var weekday = typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 1;

    var startMoment = moment();
    var endMoment = moment();

    switch (val) {
        case "1": //today
            endMoment = endMoment.add(1, "days");

            break;
        case "2": //yesterday
            startMoment = startMoment.subtract(1, "days");

            break;
        case "3": //before 2 days
            startMoment = startMoment.subtract(2, "days");
            endMoment = endMoment.subtract(1, "days");

            break;
        case "4": //before 3 days
            startMoment = startMoment.subtract(3, "days");
            endMoment = endMoment.subtract(2, "days");

            break;
        case "5": //this week
            //to work with other week start days than mon, sun
            var sub = weekday > 1 ? 1 : 0;

            startMoment = startMoment.startOf('isoWeek').isoWeekday(weekday).subtract(sub, 'week');
            endMoment = endMoment.startOf('isoWeek').isoWeekday(weekday).subtract(sub, 'week').add(1, 'week');

            break;
        case "6": //last week
            //to work with other week start days than mon, sun
            var sub = weekday > 1 ? 2 : 1;

            startMoment = startMoment.startOf('isoWeek').isoWeekday(weekday).subtract(sub, 'week');
            endMoment = endMoment.startOf('isoWeek').isoWeekday(weekday).subtract(sub, 'week').add(1, 'week');

            break;
        case "7": //this month
            startMoment = startMoment.startOf('month');
            endMoment = endMoment.startOf('month').add(1, 'month');

            break;
        case "8": //last month
            startMoment = startMoment.startOf('month').subtract(1, 'month');
            endMoment = endMoment.startOf('month');

            break;
        case "9": //last 3 months
            startMoment = startMoment.startOf('month').subtract(3, 'month');
            endMoment = endMoment.startOf('month');

            break;
    }

    date_from.datepicker('setDate', startMoment.minute(0).hour(0).toDate());
    date_to.datepicker('setDate', endMoment.minute(0).hour(0).toDate());
}

function secondsToTime(_seconds)
{
    var days = Math.floor(_seconds / (24 * 60 * 60)),
        hours               = Math.floor(_seconds / (60 * 60)),
        hours_days          = Math.floor((_seconds % (24 * 60 * 60)) / (60 * 60)),
        divisor_for_minutes = _seconds % (60 * 60),
        minutes             = Math.floor(divisor_for_minutes / 60),
        minutes_days        = Math.floor(divisor_for_minutes / 60),
        divisor_for_seconds = divisor_for_minutes % 60,
        seconds             = Math.ceil(divisor_for_seconds);

    switch (app.settings.durationFormat) {
        case 'dd:hh:mm:ss':
            return (days < 10 ? '0' : '') + days + ':' +
                   (hours_days < 10 ? '0' : '') + hours_days + ':' +
                   (minutes_days < 10 ? '0' : '') + minutes_days + ':' +
                   (seconds < 10 ? '0' : '') + seconds;
        case 'hh:mm:ss':
            return (hours < 10 ? '0' : '') + hours + ':' +
                   (minutes < 10 ? '0' : '') + minutes + ':' +
                   (seconds < 10 ? '0': '') + seconds;
        default:
            return time_lrt(seconds, minutes, hours)
    }
}

function time_lrt(seconds, minutes, hours) {
    if (hours < 0 || minutes < 0 || seconds < 0)
        return '0' + window.lang.short_s;

    return (hours ? hours+window.lang.short_h+' ' : '')+(minutes ? minutes+window.lang.short_m+" " : '')+seconds+window.lang.short_s;
}

function time_rtl(seconds, minutes, hours) {
    if (hours < 0 || minutes < 0 || seconds < 0)
        return window.lang.short_s + '0';

    return window.lang.short_s + seconds +(minutes ? " " + window.lang.short_m + minutes : '') + (hours ? ' ' +window.lang.short_h+hours : '');
}

function checkPerm(el) {
    if (el.hasClass('perm_edit') || el.hasClass('perm_remove')) {
        var parent = el.closest('tr');
        var view = parent.find('.perm_view');
        var edit = parent.find('.perm_edit').prop('checked');
        var remove = parent.find('.perm_remove').prop('checked');

        if (edit || remove) {
            if (!view.prop('checked'))
                view.trigger('click');

            view.prop('disabled', true).closest('div').addClass('disabled');
        }
        else {
            view.prop('disabled', false).closest('div').removeClass('disabled');
        }
    }
}

function checkPerms() {
    $.each($('.perm_edit, .perm_remove'), function (key, value) {
        checkPerm($(this));
    });
}

function readImage(input, target) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $(target).attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

function formBuilder($attributesContainer, attributes, values) {
    $attributesContainer = $( $attributesContainer );

    $attributesContainer.html('');

    if (values) {
        $.each(values, function( name, value )
        {
            $.each(attributes, function( index, attribute )
            {
                if (attribute.name !== name)
                    return true;

                attributes[index].default = value;
            });
        });
    }

    $.each(attributes, function( index, attribute )
    {
        var $formGroup = $('<div class="form-group"></div>');
        var value = attribute.default ? attribute.default : '';

        $formGroup.append( '<label for="'+attribute.html_name+'">'+attribute.title+':</label>' );

        switch (attribute.type) {
            case 'integer':
            case 'string':
                $formGroup.append( '<input type="text" class="form-control" name="'+attribute.html_name+'" value="'+value+'" />' );
                break;
            case 'text':
                $formGroup.append( '<textarea class="form-control" name="'+attribute.html_name+'">'+value+'</textarea>' );
                break;
            case 'datetime':
                $formGroup.append( '<input type="text" class="form-control datetimepicker" name="'+attribute.html_name+'" value="'+value+'" />' );
                $formGroup.find('.datetimepicker').datetimepicker({
                    widgetPositioning: {
                        horizontal: 'auto',
                        vertical: 'auto'
                    },
                    format: 'yyyy-mm-dd hh:ii:ss',
                    disableTouchKeyboard: true,
                    autoclose: true,
                    weekStart: typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 0,
                    language: app.lang.iso,
                    fontAwesome: true
                });
                break;
            case 'select':
                var $select = $('<select class="form-control" name="' + attribute.html_name + '"></select> ');
                $.each(attribute.options, function (index, option) {
                    $select.append('<option value="' + option.id + '">' + option.title + '</option>');
                });
                $formGroup.append($select);
                $select
                    .val(value)
                    .selectpicker();
                break;
            case 'multiselect':
                var $select = $('<select class="form-control multiexpand" name="' + attribute.html_name + '" multiple="multiple" data-live-search="true" data-actions-box="true"></select> ');
                $.each(attribute.options, function (index, option) {
                    $select.append('<option value="' + option.id + '">' + option.title + '</option>');
                });
                $formGroup.append($select);
                $select
                    .val(value)
                    .selectpicker();
                break;
        }

        if (attribute.description) {
            $formGroup.append('<small>'+attribute.description+'</small>');
        }

        var $container = $('<div class="col-sm-12"></div>');

        if (attributes.length > 1 && attribute.type != 'text' ) {
            $container = $('<div class="col-sm-6"></div>');
        }

        $container.append($formGroup);

        $attributesContainer.append($container);
    });
}

function updateUrlParameter(url, key, value) {
    // remove the hash part before operating on the url
    var i = url.indexOf('#');
    var hash = i === -1
        ? '' 
        : url.substr(i);
    url = i === -1
        ? url
        : url.substr(0, i);

    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = url.indexOf('?') !== -1 ? "&" : "?";

    if (! value) {
        // remove key-value pair if value is empty
        url = url.replace(new RegExp("([?&]?)" + key + "=[^&]*", "i"), '');
        if (url.slice(-1) === '?') {
            url = url.slice(0, -1);
        }

        // replace first occurrence of & by ? if no ? is present
        if (url.indexOf('?') === -1) {
            url = url.replace(/&/, '?');
        }
    } else if (url.match(re)) {
        url = url.replace(re, '$1' + key + "=" + value + '$2');
    } else {
        url = url + separator + key + "=" + value;
    }

    return url + hash;
}

function normalizeSerializedArray(obj) {
    var self = this,
        k,
        json = {},
        push_counters = {},
        patterns = {
            "validate": /^[a-zA-Z_][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
            "push":     /^$/,
            "fixed":    /^\d+$/,
            "named":    /^[a-zA-Z0-9_]+$/
        };

    for (k in obj) {
        if (!obj[k].hasOwnProperty('name') || !obj[k].hasOwnProperty('value')) {
            return obj;
        }
    }

    this.build = function(base, key, value){
        base[key] = value;
        return base;
    };

    this.push_counter = function(key){
        if(push_counters[key] === undefined){
            push_counters[key] = 0;
        }
        return push_counters[key]++;
    };

    $.each(obj, function(){

        // Skip invalid keys
        if(!patterns.validate.test(this.name)){
            return;
        }

        var k,
            keys = this.name.match(patterns.key),
            merge = this.value,
            reverse_key = this.name;

        while((k = keys.pop()) !== undefined){

            // Adjust reverse_key
            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

            // Push
            if(k.match(patterns.push)){
                merge = self.build([], self.push_counter(reverse_key), merge);
            }

            // Fixed
            else if(k.match(patterns.fixed)){
                merge = self.build([], k, merge);
            }

            // Named
            else if(k.match(patterns.named)){
                merge = self.build({}, k, merge);
            }
        }

        json = $.extend(true, json, merge);
    });

    return json;
}

function isHexColorDark(color) {
    if (color[0] !== '#' && color.length < 7) {
        return null;
    }

    var c = color.substring(1);      // strip #
    var rgb = parseInt(c, 16);   // convert RGB to decimal
    var r = (rgb >> 16) & 0xff;  // extract red
    var g = (rgb >>  8) & 0xff;  // extract green
    var b = (rgb >>  0) & 0xff;  // extract blue

    var luma = 0.2126 * r + 0.7152 * g + 0.0722 * b; // per ITU-R BT.709

    return luma < 40;
}

function titleRemoveYear() {
    let el = $('.datetimepicker-days tr th.switch:visible');
    let val = el.html();
    if (typeof val != 'undefined')
        el.html(val.split(' ')['0']);

    el = $('.datetimepicker-hours tr th.switch:visible');
    val = el.html();
    if (typeof val != 'undefined') {
        val = val.split(' ');
        el.html(val['0'] + ' ' + val['1']);
        $('.datetimepicker-minutes tr th.switch:visible').html(val['0'] + ' ' + val['1']);
    }

    el = $('.datetimepicker-minutes tr th.switch:visible');
    val = el.html();
    if (typeof val != 'undefined') {
        val = val.split(' ');
        el.html(val['0'] + ' ' + val['1']);
    }
}