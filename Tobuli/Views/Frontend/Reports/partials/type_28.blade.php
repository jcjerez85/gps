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
                        <th>{{ trans('validation.attributes.geofence_name') }}</th>
                        <th>{{ trans('front.shift_time') }}</th>
                        <th>{{ trans('front.late_entry') }}</th>
                        <th>{{ trans('front.late_exit') }}</th>
                        <th>{{ trans('validation.attributes.excessive_exit') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)

                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach
                                <td colspan="5">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td>{{ $row['geofence'] }}</td>
                                <td>{{ $row['shift'] }}</td>
                                <td>{{ $row['first_in'] }}</td>
                                <td>{{ $row['last_out'] }}</td>
                                <td>{{ $row['count'] }}</td>
                            </tr>
                        @endforeach
                    @endif

                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop