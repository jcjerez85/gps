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
                                <th>{{ trans('front.driver') }}</th>
                                <th>{{ trans('validation.attributes.rfid') }}</th>
                                <th>{{ trans('front.start_time') }}</th>
                                <th>{{ trans('front.end_time') }}</th>
                                <th>{{ trans('front.fuel') . ' ' . trans('front.l')}}</th>
                                <th>{{ trans('front.location') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table'] as $row)
                                <tr>
                                    <td>{{ $row['driver.name'] ?? '' }}</td>
                                    <td>{{ $row['driver.rfid'] ?? '' }}</td>
                                    <td>{{ $row['start_at'] ?? '' }}</td>
                                    <td>{{ $row['end_at'] ?? '' }}</td>
                                    <td>{{ $row['fuel_consumption'] ?? '' }}</td>
                                    <td>{!! $row['location'] ?? '' !!}</td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ trans('front.total') }}:</td>
                                <td>{{ $item['totals']['fuel_consumption']['value'] }}</td>
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