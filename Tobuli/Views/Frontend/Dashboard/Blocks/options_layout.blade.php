<div class="options-dropdown">
    <h5><b>{{ trans('front.options') }}</b></h5>
    <hr>
    {!! Form::open(['url' => route('dashboard.config_update'), 'method' => 'POST', 'class' => 'dashboard-config']) !!}
        {!! Form::hidden('block', $block) !!}
        @yield('fields')
    {!! Form::close() !!}
</div>