<div class="form-group">
    <div class="radio-inline">
        {!!Form::radio('schedule_type', 'exact_time', 1, [])!!}
        {!!Form::label(null, null)!!}
    </div>
    {!!Form::label('exact_time', trans('front.exact_time'))!!}
</div>
<div id="exact_time" class="schedule-type">
    <div class="form-group">
        {!!Form::label('exact_time[time]', trans('front.time'))!!}
        {!!Form::text('exact_time[time]', null, ['class' => 'datetimepicker form-control'])!!}
    </div>
</div>

<div class="form-group">
    <div class="radio-inline">
        {!!Form::radio('schedule_type', 'hourly', 0, [])!!}
        {!!Form::label(null, null)!!}
    </div>
    {!!Form::label('hourly', trans('front.hourly'))!!}
</div>
<div id="hourly" class="schedule-type" hidden>
    <div class="form-group">
        {!!Form::label('hourly[minute]', trans('front.minute'))!!}
        {!!Form::select('hourly[minute]', array_combine($r = range(0, 59), $r), 0, ['class' => 'form-control timeselect'])!!}
    </div>
</div>

<div class="form-group">
    <div class="radio-inline">
        {!!Form::radio('schedule_type', 'daily', 0, [])!!}
        {!!Form::label(null, null)!!}
    </div>
    {!!Form::label('daily', trans('validation.attributes.daily'))!!}
</div>
<div id="daily" class="schedule-type" hidden>
    <div class="form-group">
        {!!Form::label('daily[time]', trans('front.time'))!!}
        {!!Form::select('daily[time]', getSelectTimeRange(), '00:00', ['class' => 'form-control timeselect'])!!}
    </div>
</div>

<div class="form-group">
    <div class="radio-inline">
        {!!Form::radio('schedule_type', 'weekly', 0, [])!!}
        {!!Form::label(null, null)!!}
    </div>
    {!!Form::label('weekly', trans('validation.attributes.weekly'))!!}
</div>
<div id="weekly" class="schedule-type" hidden>
    <table class="table table-list">
        <thead>
        <tr>
            <th class="text-center"></th>
            <th class="text-center">{{ trans('front.weekday') }}</th>
            <th class="text-center">{{ trans('front.time') }}</th>
        </tr>
        </thead>
        <tbody class="text-center">
        @foreach(getSortedWeekdays() as $key => $weekday)
            <tr>
                <td>
                    <div class="checkbox">
                        {!!Form::hidden("weekly[days][$key][checked]", 0)!!}
                        {!!Form::checkbox("weekly[days][$key][checked]", 1, 0, ['class' => 'checkbox'])!!}
                        {!!Form::label(null, null)!!}
                    </div>
                </td>
                <td>
                    {{ trans($weekday) }}
                </td>
                <td>
                    {!!Form::select("weekly[days][$key][time]", getSelectTimeRange(), '00:00', ['class' => 'form-control timeselect'])!!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="form-group">
    <div class="radio-inline">
        {!!Form::radio('schedule_type', 'monthly', 0, [])!!}
        {!!Form::label(null, null)!!}
    </div>
    {!!Form::label('monthly', trans('front.monthly'))!!}
</div>
<div id="monthly" class="schedule-type" hidden>
    <div class="form-group">
        {!!Form::label('monthly[day]', ucfirst(trans('front.day')))!!}
        {!!Form::select('monthly[day]', array_combine($r = range(1, 31), $r), 0, ['class' => 'form-control'])!!}
    </div>

    <div class="form-group">
        {!!Form::label('monthly[time]', trans('front.time'))!!}
        {!!Form::select('monthly[time]', getSelectTimeRange(), '00:00', ['class' => 'form-control timeselect'])!!}
    </div>
</div>

<script>
    $('input[name="schedule_type"]').on('click', function (e) {
        var type = e.target.value;

        $('.schedule-type').each(function () {
            if ($(this).attr('id') == type)
                return $(this).show();

            return $(this).hide();
        });
    });
</script>