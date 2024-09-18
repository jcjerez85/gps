@extends('Frontend.Dashboard.Blocks.options_layout')

@section('fields')
    @include('Frontend.Dashboard.Blocks.partials.period_select', [
        'block'  => 'latest_tasks',
        'period' => $options['period']
    ])
@overwrite


