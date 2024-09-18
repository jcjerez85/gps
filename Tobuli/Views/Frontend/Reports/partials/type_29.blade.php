@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas('device') as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('validation.attributes.date') }}</th>
                    <th>{{ trans('front.from') }} ({{ trans('front.hour_short') }})</th>
                    <th>{{ trans('front.to') }} ({{ trans('front.hour_short') }})</th>
                    <th>{{ trans('front.difference') }} ({{ trans('front.hour_short') }})</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="4">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $row['from'] }}</td>
                                <td>{{ $row['to'] }}</td>
                                <td>{{ $row['diff'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>{{ trans('front.total') }} ({{ $report->globalTotals('device_count') }})</th>
                        @foreach($report->metas('device') as $meta)
                            <th></th>
                        @endforeach
                        <th></th>
                        <th></th>
                        <th>{{ $report->globalTotals('diff') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop