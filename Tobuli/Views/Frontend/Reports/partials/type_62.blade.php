@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('front.start') }}</th>
                    <th>{{ trans('front.end') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <tr>
                    @foreach($item['meta'] as $key => $meta)
                        <td>{{ $meta['value'] }}</td>
                    @endforeach
                    @if (isset($item['error']))
                            <td colspan="3">{{ $item['error'] }}</td>
                    @else
                        <td>{{ $item['totals']['odometer_start'] }}</td>
                        <td>{{ $item['totals']['odometer_end'] }}</td>
                        <td>{{ $item['totals']['distance'] }}</td>
                    @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop