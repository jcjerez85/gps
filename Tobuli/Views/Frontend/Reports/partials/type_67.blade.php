@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_title')

        <div class="panel-body">
            <table class="table">
                <tbody>
                <tr>
                    <th class="col-sm-12">{{ trans('front.devices') . ': ' . implode(', ', $report->getDeviceNames()) }}</th>
                </tr>
                <tr>
                    <th class="col-sm-12">{{ trans('front.geofences') . ': ' . implode(', ', $report->getGeofenceNames()) }}</th>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach

                    @foreach ($report->columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
                </thead>

                <tbody>
                @forelse ($report->getItems() as $item)
                    <tr>
                        @foreach($report->metas() as $key => $object)
                            <td>{{ $item['meta'][$key]['value'] }}</td>
                        @endforeach

                        @foreach ($report->columns as $key => $value)
                            <td>{{ $item['table'][$key] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="1000">{{ trans('front.nothing_found_request') }}</td>
                    </tr>
                @endforelse
                </tbody>

                @if (!empty($report->getItems()))
                    <tfoot>
                        <th style="text-align: left;" colspan="{{ count($report->metas())}}">{!! trans('global.total') !!}</th>

                        @foreach ($report->columns as $key => $value)
                            <th>{{ $report->tableTotals[$key] ?? 0 }}</th>
                        @endforeach
                    </tfoot>
                @endif

            </table>
        </div>
    </div>
@stop