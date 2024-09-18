@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        @php
            $styleHead = 'border: 1px solid #999999;vertical-align: middle;height: 60px;font-weight:bold';
            $styleBody = 'border: 1px solid #999999;vertical-align: middle;height: 60px';
            $styleFoot = 'border: 1px solid #999999;vertical-align: middle;height: 60px;font-weight:bold';
            $line = 0;
        @endphp

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="{{ $styleHead }}">#</th>
                    @foreach($report->metas() as $meta)
                        <th style="{{ $styleHead }}">{{ $meta['title'] }}</th>
                    @endforeach
                    <th style="{{ $styleHead }}">{{ trans('front.drivers') }}</th>
                    <th style="{{ $styleHead }}">{{ trans('front.work_hours') }}</th>
                    <th style="{{ $styleHead }}">{{ trans('global.distance') }}</th>
                    <th style="{{ $styleHead }}">{{ trans('front.fuel_avg') }}</th>
                    <th style="{{ $styleHead }}">{{ trans('front.fuel_consumption') }}</th>
                    <th style="{{ $styleHead }}">{{ trans('front.received_sign') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <tr>
                        <td>{{ ++$line }}</td>
                    @foreach($item['meta'] as $key => $meta)
                        <td style="{{ $styleBody }}">{{ $meta['value'] }}</td>
                    @endforeach

                    @if (isset($item['error']))
                        <td colspan="6" style="{{ $styleBody }}">{{ $item['error'] }}</td>
                    @else
                        <td style="{{ $styleBody }}">{{ $item['totals']['drivers'] }}</td>
                        <td style="{{ $styleBody }}">{{ $item['totals']['engine_hours'] }}</td>
                        <td style="{{ $styleBody }}">{{ $item['totals']['distance'] }}</td>
                        <td style="{{ $styleBody }}">{{ $item['totals']['fuel_avg'] }}</td>
                        <td style="{{ $styleBody }}">{{ $item['totals']['fuel_consumption'] }}</td>
                        <td style="{{ $styleBody }}"></td>
                    @endif
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="{{ $styleFoot }}"></td>
                        @foreach($report->metas() as $meta)
                            <td style="{{ $styleFoot }}"></td>
                        @endforeach
                        <td style="{{ $styleFoot }}"></td>
                        <td style="{{ $styleFoot }}">{{ $report->globalTotals('engine_hours') }}</td>
                        <td style="{{ $styleFoot }}">{{ $report->globalTotals('distance') }}</td>
                        <td style="{{ $styleFoot }}">{{ $report->globalTotals('fuel_avg') }}</td>
                        <td style="{{ $styleFoot }}">{{ $report->globalTotals('fuel_consumption') }}</td>
                        <td style="{{ $styleFoot }}"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop
