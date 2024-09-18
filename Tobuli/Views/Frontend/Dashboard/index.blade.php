@extends('Frontend.Layouts.default')

@section('header-menu-items')
    <li>
        <a href="{!! route('objects.index') !!}" role="button">
            <span class="icon map"></span>
            <span class="text">{!! trans('admin.map') !!}</span>
        </a>
    </li>
@stop

@section('content')
    <div id="dashboard">
        @include('Frontend.Dashboard.content')
    </div>
@stop

@section('scripts')
    @include('Frontend.Layouts.partials.app')

    @include('Frontend.Dashboard.scripts')
@stop
