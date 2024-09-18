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
                    <th>{{ trans('front.transporter') }}</th>
                    <th>{{ trans('front.consignor_name') }}</th>
                    <th>{{ trans('front.consignee_name') }}</th>
                    <th>{{ trans('front.consignee_destination') }}</th>
                    <th>{{ trans('front.datetime_dispatch') }}</th>
                    <th>{{ trans('front.datetime_unloading') }}</th>
                    <th>{{ trans('front.duration') }}</th>
                    <th>{{ trans('front.move_duration') }}</th>
                    <th>{{ trans('front.loading_location') }}</th>
                    <th>{{ trans('front.unloading_location') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                    <th>{{ trans('front.current_datetime') }}</th>
                    <th>{{ trans('front.current_location') }}</th>
                    <th>{{ trans('front.stop_duration') }}</th>
                    <th>{{ trans('front.remark') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach
                            <td colspan="15">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach ($item['journeys'] as $journey)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ !empty($journey['begin']['time']) ? $journey['begin']['time'] : '' }}</td>
                                <td>{{ !empty($journey['end']['time']) ? $journey['end']['time'] : '' }}</td>
                                <td>{{ !empty($journey['duration']) ? $journey['duration'] : '' }}</td>
                                <td>{{ !empty($journey['move_duration']) ? $journey['move_duration'] : '' }}</td>
                                <td>{{ !empty($journey['begin']['address']) ? $journey['begin']['address'] : '' }}</td>
                                <td>{{ !empty($journey['end']['address']) ? $journey['end']['address'] : '' }}</td>
                                <td>{{ !empty($journey['distance']) ? $journey['distance'] : '' }}</td>
                                <td>{{ $item['device']['time'] }}</td>
                                <td>{{ $item['device']['address'] }}</td>
                                <td>{{ !empty($journey['stop_duration']) ? $journey['stop_duration'] : '' }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop