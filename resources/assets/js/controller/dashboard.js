function Dashboard()
{
    var
        _this = this;

    _this.init = function ()
    {
        var modal = $modal.initModal();

        modal.load(app.urls.dashboard, '', function () {
            modal.modal();
        });
    };

    _this.close = function ()
    {
        $('#dashboard .close').click();
    };

    _this.loadBlockContent = function (name, withoutLoader)
    {
        withoutLoader = withoutLoader || false;

        var
            block = $('#dashboard_blocks').find($('#block_'+name)),
            block_body = block.find('.dashboard-content');

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: app.urls.dashboardBlockContent,
            beforeSend: function () {
                if ( ! withoutLoader)
                    loader.add(block_body);
            },
            data: {
                name: name
            },
            success: function (res) {
                if (res.status == 0) {
                    block.remove();
                    return;
                }

                block_body.html(res.html);
            },
            complete: function () {
                loader.remove(block_body);
            }
        });
    };

    _this.initEvents = function ()
    {
        $(document).on('change', 'form.dashboard-config :input', function () {
            var form = $(this).closest('form'),
                query = form.serialize(),
                block = form.find('input[name="block"]').val();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: form.attr('action') + '?' + query,
                beforeSend: function() {
                    loader.add( form );
                },
                success: function (response) {
                    _this.loadBlockContent(block);
                },
                complete: function() {
                    loader.remove( form );
                }
            });

        });
    }
}