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
                    <th>CAN Bus {{ trans('front.engine_hours') }}</th>
                    <th>{{ trans('front.virtual_engine_hours') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <tr>
                        @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                        @endforeach
                        @if (isset($item['error']))
                            <td colspan="2">{{ $item['error'] }}</td>
                        @else
                            <td>{{ $item['totals']['hours'] }}</td>
                            <td>{{ $item['totals']['virtual_hours'] }}</td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop