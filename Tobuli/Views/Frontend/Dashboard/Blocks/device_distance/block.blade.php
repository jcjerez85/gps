@extends('Frontend.Dashboard.Blocks.layout')

@section('width', 'col-md-12')

@section('header')
    <a href="javascript:" onclick="app.dashboard.close()" style="text-decoration: none !important;">
        <i class="icon device-distance"></i>
        {{ trans('front.distance_travelled') }}
    </a>
@stop