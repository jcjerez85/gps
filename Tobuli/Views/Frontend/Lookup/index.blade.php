@extends('Frontend.Layouts.default')

@section('content')
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title"><i class="{{ $lookup->getIcon() }}"></i> {{ $lookup->getTitle() }}</div>
    </div>
    <div class="panel-body">
        @include('front::Lookup.partials.table')
    </div>
</div>
@stop

