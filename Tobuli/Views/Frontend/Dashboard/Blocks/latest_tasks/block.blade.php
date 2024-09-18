@extends('Frontend.Dashboard.Blocks.layout')

@section('header')
    <a href="javascript:" onclick="$(`[data-modal='tasks']`).click()" style="text-decoration: none !important;">
        <i class="icon task"></i>
        {{ trans('front.tasks') }}
    </a>
@stop