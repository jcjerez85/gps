<div id="device-config-fields">
    <div class="alert alert-success hidden">
        {{ trans('global.success') }}
    </div>

    <div class="alert alert-danger hidden">
        Error
    </div>
    <div class="row no-padding">
        <div class="col-xs-12">
            @if($showDeviceSelect ?? true)
            <div class="form-group">
                {!! Form::label('device_id', trans('global.device'), ['class' => 'required']) !!}
                {!! Form::select('device_id', $devices, $device_id ?? null, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
            </div>
            @endif
            <div class="form-group">
                {!! Form::label('config_id', trans('validation.attributes.brand'), ['class' => 'required']) !!}
                {!! Form::select('config_id', $device_configs, null, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                <small>{{ trans('front.device_config_disclaimer') }}</small>
            </div>
            <div class="form-group">
                {!! Form::label('apn_config', trans('front.apn_configuration'), ['class' => 'required']) !!}
                {!! Form::select('apn_config', $apn_configs, null, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
            </div>
            <div class="form-group">
                {!!Form::label('apn_name', trans('validation.attributes.apn_name'), ['class' => 'required'])!!}
                {!!Form::text('apn_name', null, ['class' => 'form-control'])!!}
            </div>
            <div class="row">
                <div class="form-group col-xs-12 col-md-6">
                    {!!Form::label('apn_username', trans('validation.attributes.apn_username'))!!}
                    {!!Form::text('apn_username', null, ['class' => 'form-control'])!!}
                </div>
                <div class="form-group col-xs-12 col-md-6">
                    {!!Form::label('apn_password', trans('validation.attributes.apn_password'))!!}
                    {!!Form::text('apn_password', null, ['class' => 'form-control'])!!}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function device_config_modal_callback(res) {
        var wrapper = $('#device_config');
        var alert = null;

        if (typeof res.error != 'undefined') {
            alert = wrapper.find('.alert-danger');
        } else {
            alert = wrapper.find('.alert-success');
        }

        if ($(alert).hasClass('hidden')) {
            $(alert).removeClass('hidden');
        }

        $(alert).slideDown(600);

        setTimeout(function() {
            wrapper.find('.alert').slideUp(800);
        }, 3000);
    }

    $('#device-config-fields #apn_config').on('change', function() {
        var id = $(this).find('option:selected').val();
        var container = $('#device-config-fields');

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: app.urls.deviceConfigApnData + id,
            beforeSend: function () {
                loader.add(container.closest('.modal-content'));
            },
            success: function(res) {
                if (res.success !== undefined) {
                    fillApnData(res.success, container);
                }
            },
            complete: function () {
                loader.remove(container.closest('.modal-content'));
            }
        });
    }).change();

    function fillApnData(data, container) {
        container.find('#apn_name').val(data.apn_name);
        container.find('#apn_username').val(data.apn_username);
        container.find('#apn_password').val(data.apn_password);
    }
</script>
