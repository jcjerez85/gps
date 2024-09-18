<p>{{ trans('front.time_period') }}</p>

<div class="radio">
    {!! Form::radio("dashboard[blocks][$block][options][period]", 'day', $period == 'day') !!}
    {!! Form::label(null, ucfirst(trans('front.day'))) !!}
</div>

<div class="radio">
    {!! Form::radio("dashboard[blocks][$block][options][period]", 'week', $period == 'week') !!}
    {!! Form::label(null, trans('front.week')) !!}
</div>

<div class="radio">
    {!! Form::radio("dashboard[blocks][$block][options][period]", 'month', $period == 'month') !!}
    {!! Form::label(null, ucfirst(trans('front.month'))) !!}
</div>