{!! Form::open(['route' => 'sharing.send', 'method' => 'POST', 'id' => 'sharing_send']) !!}
    <div class="form-group">
        <div class="radio-inline">
            {!! Form::radio('expiration_by', 'none', true) !!}
            {!! Form::label('expiration_by', trans('front.none') ) !!}
        </div>
        <div class="radio-inline">
            {!! Form::radio('expiration_by', 'duration', false) !!}
            {!! Form::label('expiration_by', trans('front.duration') ) !!}
        </div>
        <div class="radio-inline">
            {!! Form::radio('expiration_by', 'date', false) !!}
            {!! Form::label('expiration_by', trans('global.date') ) !!}
        </div>
    </div>

    <div class="form-group expiration_by expiration_by_date">
        {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}
        {!! Form::text('expiration_date', null, ['class' => 'form-control datetimepicker']) !!}
    </div>

    <div class="form-group expiration_by expiration_by_duration">
        {!! Form::label('duration', trans('front.duration').':') !!}
        <div class="input-group">
            {!! Form::select('duration', $durationTimes, null, ['class' => 'form-control', 'data-icon' => 'icon time']) !!}
        </div>
    </div>

    <div class="form-group delete_after_expiration">
        <div class="checkbox">
            {!! Form::hidden('delete_after_expiration', 0) !!}
            {!! Form::checkbox('delete_after_expiration', 1, false) !!}
            {!! Form::label('delete_after_expiration', trans('validation.attributes.delete_after_expiration') ) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
        {!! Form::select('devices[]', $devices, $selectedDevices, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>

    @if (Auth::user()->canSendSms())
    <div class="form-group">
        {!! Form::label('sms', trans('validation.attributes.sms').':') !!}
        <div class="input-group">
            <div class="checkbox input-group-btn">
                {!! Form::hidden('send_sms', 0) !!}
                {!! Form::checkbox('send_sms', 1, false, ['data-disabler' => '#sms;disable']) !!}
                {!! Form::label(null) !!}
            </div>
            {!! Form::text('sms', null, ['class' => 'form-control']) !!}
        </div>
        <small>{!! trans('front.sms_semicolon') !!}</small>
    </div>
    @endif

    <div class="form-group">
        {!! Form::label('email', trans('validation.attributes.email').':') !!}
        <div class="input-group">
            <div class="checkbox input-group-btn">
                {!! Form::hidden('send_email', 0) !!}
                {!! Form::checkbox('send_email', 1, false, ['data-disabler' => '#email;disable']) !!}
                {!! Form::label(null) !!}
            </div>
            {!! Form::text('email', null, ['class' => 'form-control']) !!}
        </div>
        <small>{!! trans('front.email_semicolon') !!}</small>
    </div>
{!! Form::close() !!}

<script>
    $(document).ready(function() {
        $('input[name="send_via"]').on('change', function() {
            var value = $(this).val(),
                checked = $(this).is(':checked');

            $('.send_via').hide();

            if ( ! checked)
                return;

            $('.send_via_' + value).show();
        });
        $('input[name="send_via"]:checked').trigger('change');

        $('input[name="expiration_by"]').on('change', function() {
            var value = $(this).val(),
                checked = $(this).is(':checked');

            $('.expiration_by, .form-group.delete_after_expiration').hide();

            if ( ! checked)
                return;

            $('.expiration_by_' + value).show();

            if (['duration', 'date'].includes(value)) {
                $('.form-group.delete_after_expiration').show();
            }
        });
        $('input[name="expiration_by"]:checked').trigger('change');
    });
</script>
