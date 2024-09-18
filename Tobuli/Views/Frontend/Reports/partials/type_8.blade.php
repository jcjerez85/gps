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
                    <th>{{ trans('front.driver') }}</th>
                    <th>{{ trans('front.time') }}</th>
                    <th>{{ trans('front.event') }}</th>
                    <th>{{ trans('front.position') }}</th>
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
                        @foreach ($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['driver'] }}</td>
                                <td>{{ $row['time'] }}</td>
                                <td>{{ $row['message'] }}</td>
                                <td>{!! $row['location'] !!}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                @foreach($report->globalTotals() as $title => $value)
                    <tr>
                        @foreach($report->metas('device') as $meta)
                            <th></th>
                        @endforeach
                        <th></th>
                        <th></th>
                        <th>{{ $title }}</th>
                        <th>{{ $value }}</th>
                    </tr>
                @endforeach
                </tfoot>
            </table>
        </div>
    </div>
@stop