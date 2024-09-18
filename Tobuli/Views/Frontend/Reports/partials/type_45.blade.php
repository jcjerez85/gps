@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')
        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('global.date') }}</th>
                    <th>{{ trans('front.location') }}</th>
                    <th>{{ trans('front.overspeed_duration') }}</th>
                    <th>{{ trans('front.top_speed') }}</th>
                    <th>{{ trans('front.driver') }}</th>
                    <th>{{ trans('validation.attributes.phone') }}</th>
                    <th>{{ trans('validation.attributes.description') }}</th>
                </tr>
                </thead>
                <tbody>
                    <?php $rowCount = 1; ?>
                    @foreach ($report->getItems() as $item)
                        @if (isset($item['error']))
                            <tr>
                                <td>{{ '-' }}</td>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td colspan="7">{{ $item['error'] }}</td>
                            </tr>
                        @else
                            @foreach($item['table']['rows'] as $row)
                            <tr>
                                <td>{{ $rowCount++ }}</td>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['start_at'] }}</td>
                                <td>{!! $row['location'] !!}</td>
                                <td>{{ $row['overspeed_duration'] }}</td>
                                <td>{{ $row['speed_max'] }}</td>
                                <td>{{ $row['driver'] }}</td>
                                <td>{{ $row['phone'] }}</td>
                                <td>{{ $row['description'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
