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
                    @foreach ($report->columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
                </thead>

                <tbody>
                @forelse ($report->table as $shiftStats)
                    <tr>
                        @foreach ($report->columns as $key => $value)
                            <td>{{ $shiftStats[$key] ?? 0 }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($report->columns) }}">{{ trans('front.nothing_found_request') }}</td>
                    </tr>
                @endforelse
                </tbody>

                @if (!empty($report->tableTotals))
                    <tfoot>
                        @foreach ($report->columns as $key => $value)
                            <th>{{ $report->tableTotals[$key] ?? 0 }}</th>
                        @endforeach
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@stop