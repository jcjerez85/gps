@php /** @var \Tobuli\Entities\User $item */ @endphp

<div class="form-group">
    <div class="checkbox">
        {!! Form::checkbox(null, 1, $item->login_periods ?? false, ['id' => 'edit_login_periods']) !!}
        {!! Form::label(null, trans('validation.attributes.login_periods')) !!}
    </div>
</div>

<hr>

<div data-disablable="#edit_login_periods;enable">
    <input type="hidden" name="login_periods" value="">
</div>
<div data-disablable="#edit_login_periods;disable">
    @include('Frontend.Alerts.partials.schedules', ['schedules' => $loginPeriods, 'schedulesInputName' => 'login_periods'])
</div>

<script>
    $(document).ready(function() {
        var dragger = new Dragger();
        dragger.int();

        $(document).on('change', '#edit_login_periods', function() {
            dragger.disable();

            if ($(this).is(':checked')) {
                dragger.enable();
            }
        });

        $('#edit_login_periods').trigger('change');
    });
</script>
