@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        @foreach($item['meta'] as $meta)
                            <th>{{ $meta['title'] }}</th>
                        @endforeach
                        <th>{{ trans('front.time') }}</th>
                        <th>{{ trans('front.speed') }} GPS</th>
                        <th>{{ trans('front.speed') }} ECM</th>
                        <th>{{ trans('front.difference') }}</th>
                        <th>{{ trans('front.tachometer') }}</th>
                        <th>{{ trans('front.location') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if (isset($item['error']))
                        <tr>
                            <td colspan="20">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach

                                <td>{{ $row['time'] }}</td>
                                <td>{{ $row['speed'] }}</td>
                                <td>{{ $row['speed_ecm'] }}</td>
                                <td>{{ $row['difference'] }}</td>
                                <td>{{ $row['tachometer'] }}</td>
                                <td>{!! $row['location'] !!}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop