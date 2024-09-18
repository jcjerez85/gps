<div class="widget" id="widget-template-webhook" >
    @if (!empty($templates))
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon send"></i> {{ trans('front.template_webhook') }}
        </div>
    </div>

    <div class="widget-body" style="width: 200px;">
        {!!Form::open(['route' => ['device.widgets.template_webhook_send', $device->id], 'method' => 'POST', ''])!!}
        <div class="form-group">
            {!!Form::select('template_id', $templates, null, ['class' => 'form-control', 'data-container' => 'body'])!!}
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-info btn-block">{{ trans('global.apply') }}</button>
        </div>
        {!!Form::close()!!}
    </div>

    <script>
        var $temaplteWidget = $('#widget-template-webhook');

        initComponents($temaplteWidget);

        $('select[name="template_id"]', $temaplteWidget)
            .on('change', function (e) {
                console.log('template_id', $(this).val());
                if ($(this).val() != 0) {
                    console.log('show');
                   $('button[type="submit"]', $temaplteWidget).show();
                } else {
                    console.log('hide');
                    $('button[type="submit"]', $temaplteWidget).hide();
                }
            })
            .trigger('change');

        $('form', $temaplteWidget).on('submit', function (e) {
            var $form = $(this);
            e.preventDefault();

            $.ajax({
                type: 'POST',
                dataType: "json",
                data: $(this).serialize(),
                url: $(this).attr('action'),
                beforeSend: function () {
                    loader.add($form);
                },
                success: function (res) {},
                complete: function () {
                    loader.remove($form);
                }
            });
        });
    </script>
    @endif
</div>