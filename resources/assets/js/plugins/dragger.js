function Dragger() {
    var
        _this = this,
        dragSelect = null,
        $container = null,
        $buttons = null,
        options = {
            containerSelector: '.table-weektime',
            itemSelector: '.item',
            buttonSelector: '[data-dragger-set]'
        };

    _this.events = function()
    {
        $buttons.on('click', function() {
            _this.set($(this).attr('data-dragger-set'));
        });
    }

    _this.int = function() {
        $container = $(options.containerSelector);
        $buttons = $(options.buttonSelector);

        dragSelect = new DragSelect({
            selectables: document.querySelectorAll(options.containerSelector + ' ' + options.itemSelector),
            multiSelectMode: true,
            onElementSelect: function (node) {
                $('input[type="checkbox"]', node).attr('checked', 'checked').prop('checked', true);
            },
            onElementUnselect: function (node) {
                $('input[type="checkbox"]', node).removeAttr('checked').prop('checked', false);
            }
        });

        _this.events();
        _this.set('checked');
    };

    _this.set = function(type) {
        switch (type) {
            case 'monday':
            case 'tuesday':
            case 'wednesday':
            case 'thursday':
            case 'friday':
            case 'saturday':
            case 'sunday':
                $items = $(options.itemSelector + '[data-day="'+type+'"]', $container);
                dragSelect.addSelection($items);
                break;
            case 'workdays':
                dragSelect.clearSelection();
                $items = $(
                    options.itemSelector + '[data-day="monday"],' +
                    options.itemSelector + '[data-day="tuesday"], ' +
                    options.itemSelector + '[data-day="wednesday"], ' +
                    options.itemSelector + '[data-day="thursday"], ' +
                    options.itemSelector + '[data-day="friday"]'
                    , $container);
                dragSelect.addSelection($items);
                break;
            case 'weekend':
                dragSelect.clearSelection();
                $items = $(
                    options.itemSelector + '[data-day="saturday"], ' +
                    options.itemSelector + '[data-day="sunday"]'
                    , $container);
                dragSelect.addSelection($items);
                break;
            case 'always':
                $items = $(options.itemSelector + '', $container);
                dragSelect.addSelection($items);
                break;
            case 'checked':
                $items = $(options.itemSelector + ':has(input[type="checkbox"]:checked)', $container);
                dragSelect.addSelection($items);
                break;
        }
    };

    _this.disable = function()
    {
        $container.addClass('disabled');
        $buttons.attr('disabled', 'disabled').css('pointerEvents', 'none');
        dragSelect.stop();
    }

    _this.enable = function()
    {
        $container.removeClass('disabled');
        $buttons.removeAttr('disabled').css('pointerEvents', 'auto');
        dragSelect.start();
    }

    _this.destroy = function()
    {
        if ( ! dragSelect)
            return;

        dragSelect.stop();
        dragSelect = null;

        $('.ds-selector').remove();
    };
}