@extends('Frontend.Dashboard.Blocks.layout')

@section('header')
    <a href="javascript:" onclick="app.dashboard.close();app.openTab('alerts_tab');" style="text-decoration: none !important;">
        <i class="icon alerts"></i>
        {{ trans('front.alerts_count') }}
    </a>
@stop