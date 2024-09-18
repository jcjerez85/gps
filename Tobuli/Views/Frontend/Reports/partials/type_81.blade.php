@extends('front::Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('front::Reports.partials.item_heading')

        <div class="panel-heading">
            {{ trans('front.shift_time') }}: {{ $report->getShiftTime() }}
        </div>

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('global.distance') }}</th>
                    <th>{{ trans('front.move_duration') }}</th>
                    <th>{{ trans('front.stop_duration') }}</th>
                    <th>{{ trans('front.engine_hours') }}</th>
                    <th>{{ trans('front.top_speed') }}</th>
                    <th>{{ trans('front.fuel_consumption') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($report->getItems() as $item)
                    <tr>
                        @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                        @endforeach

                        @if (isset($item['error']))
                            <td colspan="6">{{ $item['error'] }}</td>
                        @else
                            <td>{{ $item['totals']['distance'] }}</td>
                            <td>{{ $item['totals']['drive_duration'] }}</td>
                            <td>{{ $item['totals']['stop_duration'] }}</td>
                            <td>{{ $item['totals']['engine_hours'] }}</td>
                            <td>{{ $item['totals']['speed_max'] }}</td>
                            <td>{{ $item['totals']['fuel_consumption'] }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($report->metas()) + 6 }}">{{ trans('front.nothing_found_request') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop