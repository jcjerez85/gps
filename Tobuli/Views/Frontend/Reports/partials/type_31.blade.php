<?php
    $items = $report->getItems();

    $emptyItems = array_filter($items,
        function($item) {
            return isset($item['error']);
        });
?>

@extends('Frontend.Reports.partials.layout')

@section('content')

    @include('Frontend.Reports.partials.report_empty')

    @foreach ($items as $item)
        @if ( ! isset($item['error']))
            <div class="panel panel-default">
                @include('Frontend.Reports.partials.item_heading')

                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('front.zone_out') }}</th>
                                <th>{{ trans('front.area_starting') }}</th>
                                <th>{{ trans('front.zone_in') }}</th>
                                <th>{{ trans('front.area_arrival') }}</th>
                                <th>{{ trans('front.duration') }}</th>
                                <th>{{ trans('global.distance') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                @if(empty($last_date) || $last_date != $row['date'])
                                    <tr>
                                        <td><strong>{{ $last_date = $row['date'] }}</strong></td>
                                        <td colspan="5"></td>
                                    </tr>
                                @endif

                                <tr>
                                    <td>{{ $row['left_time'] }}</td>
                                    <td>{{ $row['left_name'] }}</td>
                                    <td>{{ $row['enter_time'] }}</td>
                                    <td>{{ $row['enter_name'] }}</td>
                                    <td>{{ $row['duration'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>{{ trans('front.total') }}</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>{{ $item['table']['totals']['distance'] }}</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    @endforeach

    <div class="panel panel-default">
        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ trans('front.total') }}</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <th>{{ trans('global.distance') }}</th>
                    <td>{{ $report->globalTotals('distance') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if (empty($items) || count($items) == count($emptyItems))
        <span>{{ trans('front.report_no_results') }}</span>
    @endif
@stop