@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                @php $char = 'A' @endphp

                <thead>
                <tr>
                    @foreach($report->metas() as $meta)
                        @php $char++ @endphp
                        <th>{{ $meta['title'] }}</th>
                    @endforeach

                    <th>{{ trans('front.driver') }}</th>
                    <th>{{ trans('front.geofences') }}</th>
                    <th>{{ trans('front.driving_score') }}</th>
                    <th>{{ trans('global.distance') }}</th>
                    <th>{{ trans('front.overspeed_count') }}</th>
                    <th>{{ trans('front.overspeed_score') }}</th>
                    <th>{{ trans('front.harsh_acceleration_count') }}</th>
                    <th>{{ trans('front.harsh_acceleration_score') }}(/100kms)</th>
                    <th>{{ trans('front.harsh_braking_count') }}</th>
                    <th>{{ trans('front.harsh_braking_score') }}(/100kms)</th>
                    <th>{{ trans('front.harsh_turning_count') }}</th>
                    <th>{{ trans('front.harsh_turning_score') }}(/100kms)</th>
                </tr>
                </thead>

                @php $line = 1 @endphp

                <tbody>
                @foreach ($report->getItems() as $item)
                    @if (isset($item['error']))
                        @php $line++ @endphp
                        <tr>
                            @foreach($item['meta'] as $key => $meta)
                                <td>{{ $meta['value'] }}</td>
                            @endforeach

                            <td colspan="12">{{ $item['error'] }}</td>
                        </tr>
                    @else
                        @foreach($item['table']['rows'] as $row)
                            <tr>
                                @foreach($item['meta'] as $key => $meta)
                                    <td>{{ $meta['value'] }}</td>
                                @endforeach

                                @if ($report->getFormat() == 'xls')
                                    @php $line++ @endphp
                                    @php $dist = chr(ord($char) + 3) . $line @endphp
                                    @php $totalScore = '100-' . chr(ord($char) + 5) . $line . '-' . chr(ord($char) + 7) . $line . '-' . chr(ord($char) + 9) . $line . '-' . chr(ord($char) + 11) . $line @endphp

                                    <td>{{ $row['drivers'] }}</td>
                                    <td>{{ \Illuminate\Support\Arr::get($row, 'geofences_in') }}</td>
                                    <td>={{ $totalScore }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                    <td>{{ $row['overspeed_count'] }}</td>
                                    <td>={{ chr(ord($char) + 4) . $line }}/{{ $dist }}*100</td>
                                    <td>{{ $row['ha'] }}</td>
                                    <td>={{ chr(ord($char) + 6) . $line }}/{{ $dist }}*100</td>
                                    <td>{{ $row['hb'] }}</td>
                                    <td>={{ chr(ord($char) + 8) . $line }}/{{ $dist }}*100</td>
                                    <td>{{ $row['ht'] }}</td>
                                    <td>={{ chr(ord($char) + 10) . $line }}/{{ $dist }}*100</td>
                                @else
                                    <td>{{ $row['drivers'] }}</td>
                                    <td>{{ \Illuminate\Support\Arr::get($row, 'geofences_in') }}</td>
                                    <td>{{ $row['rag'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                    <td>{{ $row['overspeed_count'] }}</td>
                                    <td>{{ $row['score_overspeed'] }}</td>
                                    <td>{{ $row['ha'] }}</td>
                                    <td>{{ $row['score_harsh_a'] }}</td>
                                    <td>{{ $row['hb'] }}</td>
                                    <td>{{ $row['score_harsh_b'] }}</td>
                                    <td>{{ $row['ht'] }}</td>
                                    <td>{{ $row['score_harsh_t'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($report->getFormat() != 'xls')
        <div class="panel panel-default">
            <div class="panel-body no-padding" style="padding: 0px;">
                <table class="table" style="table-layout: auto; margin-bottom: 0px;">
                    <tbody>
                    <tr>
                        <td style="width: 150px;">D</td>
                        <td>{{ trans('front.distance_driver') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">OD</td>
                        <td>{{ trans('front.overspeed_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">AC</td>
                        <td>{{ trans('front.harsh_acceleration_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">AS = AC / D * 100</td>
                        <td>{{ trans('front.harsh_acceleration_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">BC</td>
                        <td>{{ trans('front.harsh_braking_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">BS = BC / D * 100</td>
                        <td>{{  trans('front.harsh_braking_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">TC</td>
                        <td>{{ trans('front.harsh_turning_count') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">TS = TC / D * 100</td>
                        <td>{{  trans('front.harsh_turning_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">OS = OD / D * 100</td>
                        <td>{{ trans('front.overspeed_score') }}</td>
                    </tr>
                    <tr>
                        <td style="width: 150px;">R = 100 - AS - BS - TS - OS</td>
                        <td>{{ trans('front.driving_score') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@stop