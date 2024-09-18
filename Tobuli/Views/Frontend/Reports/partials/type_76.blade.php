@extends('front::Reports.partials.layout')
@php /** @var \Tobuli\Reports\Report $report */ @endphp
@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('front::Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('front::Reports.partials.item_empty')
            @else
                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('global.device') }}</th>
                                <th>{{ trans('front.start_time') }}</th>
                                <th>{{ trans('front.end_time') }}</th>
                                <th>{{ trans('front.fuel') . ' ' . trans('front.l')}}</th>
                                <th>{{ trans('front.route_length') . ' ' . trans('front.km')}}</th>
                                <th>{{ trans('front.fuel_consumption') . ' ' . trans('front.l_km') }}</th>
                                <th>{{ trans('front.engine_hours') . ' ' . trans('front.h')}}</th>
                                <th>{{ trans('front.fuel_consumption') . ' ' . trans('front.l_h') }}</th>
                                <th>{{ trans('front.location') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table'] as $row)
                                <tr>
                                    <td>{{ $row['tank_name'] ?? '' }}</td>
                                    <td>{{ $row['start_at'] ?? '' }}</td>
                                    <td>{{ $row['end_at'] ?? '' }}</td>
                                    <td>{{ $row['fuel_consumption'] ?? '' }}</td>
                                    @if(isset($row['no_data']))
                                        <td colspan="5">{{ trans('admin.no_data') }}</td>
                                    @else
                                        <td>{{ $row['distance'] ?? '' }}</td>
                                        <td>{{ $row['fuel_100'] ?? '' }}</td>
                                        <td>{{ $row['engine_hours'] ?? '' }}</td>
                                        <td>{{ $row['fuel_h'] ?? '' }}</td>
                                        <td>{!! $row['location'] ?? '' !!}</td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ trans('front.total') }}:</td>
                                    <td>{{ $item['totals']['fuel_consumption']['value'] }}</td>
                                    <td>{{ $item['totals']['distance']['value'] ?? 0 }}</td>
                                    <td>{{ $item['totals']['fuel_100']['value'] ?? 0 }}</td>
                                    <td>{{ $item['totals']['engine_hours']['value'] ?? 0 }}</td>
                                    <td>{{ $item['totals']['fuel_h']['value'] ?? 0 }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach

    @if(count($report->getItems()))
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td class="col-sm-6">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>{{ trans('front.total') }} {{ trans('front.fuel') }}: {{ $report->globalTotals('fuel_consumption') }}</th>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
@stop