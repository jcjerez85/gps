function Alert(data) {
    var
        _this = this,
        defaults = {
            id: null,
            name: 'N/A',
            active: true,
        },
        options = {};

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

    _this.create = function(data) {
        $( document ).trigger('alert.create', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        _this.searchValue = options.name.toLowerCase();

        $( document ).trigger('alert.created', _this);
    };

    _this.update = function(data) {
        $( document ).trigger('alert.update', _this);

        data = data || {};

        options = $.extend({}, defaults, data);

        _this.searchValue = options.name.toLowerCase();

        $( document ).trigger('alert.updated', _this);
    };

    _this.create(data);
}
