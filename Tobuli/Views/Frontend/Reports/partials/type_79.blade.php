@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ trans('front.driver') }}</th>
                    @foreach($report->metas() as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('global.date') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                    @if (settings('plugins.business_private_drive.status'))
                    <th>{{ trans('front.drive_business') }}</th>
                    <th>{{ trans('front.drive_private') }}</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @forelse ($report->getItems() as $item)
                    <tr>
                        <td>{!! $item['driver_name'] !!}</td>

                        @foreach($item['meta'] as $key => $meta)
                            <td>{{ $meta['value'] }}</td>
                        @endforeach

                        <td>{{ $item['date'] }}</td>
                        <td>{{ $item['distance'] }}</td>

                        @if (settings('plugins.business_private_drive.status'))
                        <td>{{ $item['distance_business'] }}</td>
                        <td>{{ $item['distance_private'] }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">{{ trans('front.nothing_found_request') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop