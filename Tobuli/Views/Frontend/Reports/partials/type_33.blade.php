@extends('Frontend.Reports.partials.layout')

@section('styles')
    <style>
        .bottomline {
            border-bottom: 1px dotted !important;
        }

        .field {
            white-space: nowrap;
            width: 1%;
            font-weight: bold;
        }
    </style>
@stop

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else
                <div class="panel-body">
                    <table style="margin-bottom: 0;" class="table">
                        <tr>
                            <td width="50%" style="padding-right: 40px;">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="field">{{ trans('front.koc_group') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.vehicle_registration_number') }}:</td>
                                        <td class="bottomline">{{ $item['meta']['device.registration_number']['value'] ?? null }}</td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.contract_number') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.contract_title') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.contractor_name') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.controlling_team') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    <tr>
                                        <td class="field">{{ trans('front.vehicle_pass_expiry_date') }}:</td>
                                        <td class="bottomline"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="50%">
                                <table class="table" style="padding: 10px">
                                    <tbody>
                                    <tr>
                                        <td class="field">
                                            {!! trans('front.total') . ' ' . trans('front.travel_time') !!}:
                                        </td>
                                        <td class="bottomline">{{ $item['totals']['duration']['value'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="field">
                                            {!! trans('front.total') . ' ' . trans('global.distance') !!}:
                                        </td>
                                        <td class="bottomline">{{ $item['totals']['distance']['value'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="field">{!! trans('front.number_of_overspeeds') !!}:</td>
                                        <td class="bottomline">{{ $item['totals']['overspeed_count']['value'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="field">{!! trans('front.highest_recorded_speed') !!}:
                                        </td>
                                        <td class="bottomline">{{ $item['totals']['speed_max']['value'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="field">{!! trans('front.speed_perf_percent') !!}:</td>
                                        <td class="bottomline">
                                            {{ round(100 - (floatval($item['totals']['overspeed_distance']['value'] ?? 0) / floatval($item['totals']['distance']['value'] ?? 1) * 100), 1)  }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="field">{!! trans('front.time_period') !!}:</td>
                                        <td class="bottomline">{{ Formatter::time()->human($report->getDateFrom()) }}
                                            - {{ Formatter::time()->human($report->getDateTo()) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="panel-body no-padding">
                    <table class="table table-hover" style="margin-bottom: 0px">
                        <thead>
                        <tr>
                            <th>#SL</th>
                            <th>{{ trans('global.date') }}</th>
                            <th>{{ trans('validation.attributes.plate_number') }}</th>
                            <th>{{ trans('front.overspeed_location') }}</th>
                            <th>{{ trans('front.recorded_speed') }}</th>
                            <th>{{ trans('front.heading_direction') }}</th>
                            <th>{{ trans('front.allowed_speed_limit') }}</th>
                            <th>{{ trans('front.start_time') }}</th>
                            <th>{{ trans('front.end_time') }}</th>
                            <th>{{ trans('front.overspeed_duration_os') }}</th>
                            <th>{{ trans('front.overspeed_distance_os') }}</th>
                            <th>{{ trans('front.tamper_signs') }}</th>
                            <th>{{ trans('front.seatbelt_usage') }}</th>
                            <th>{{ trans('front.remark') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $plate_number = $item['meta']['device.plate_number']['value'] ?? null; ?>

                        @foreach ($item['table']['rows'] as $index => $row)
                            <tr>
                                <td>{{ $index }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $plate_number }}</td>
                                <td>{!! $row['location'] !!}</td>
                                <td>{{ $row['speed_max'] }}</td>
                                <td>{{ $row['course'] }}</td>
                                <td>{{ $report->getSpeedLimit() }}</td>
                                <td>{{ $row['start_at'] }}</td>
                                <td>{{ $row['end_at'] }}</td>
                                <td>{{ $row['duration'] }}</td>
                                <td>{{ $row['distance'] }}</td>
                                <td>{{ (is_null($row['gsm']) || $row['gsm'] > 5) ? 'false' : 'true' }}</td>
                                <td>{{ ($item['totals']['seatbelt_off_duration']['value'] != '0s') ? 'false' : 'true' }}</td>
                                <td>
                                    {{ ($item['totals']['harsh_acceleration_count']['value'] > 0) ? 'harsh_acceleration' : '' }}
                                    {{ ($item['totals']['harsh_breaking_count']['value'] > 0) ? 'harsh_breaking' : '' }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
@stop