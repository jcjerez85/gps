@if (!Auth::User()->perm('send_command', 'view'))
    <div class="alert alert-danger" role="alert">{{ trans('front.dont_have_permission') }}</div>
@else
    <div class="form-group">
        {!!Form::label('device_id[]', trans('validation.attributes.devices').':')!!}
        {!!Form::select('device_id[]', $devices_gprs, (isset($device_id) ? $device_id : null), ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true'])!!}
    </div>
    <div class="form-group send-command-type">
        {!!Form::label('type', trans('validation.attributes.type').':')!!}
        {!!Form::select('type', $commands, (isset($command) ? $command : null), ['class' => 'form-control'])!!}
    </div>
    <div class="row attributes"></div>
@endif