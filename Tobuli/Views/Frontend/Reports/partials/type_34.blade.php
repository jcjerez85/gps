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
    <div class="panel panel-default">
        <div class="panel-body">
            <table style="margin-bottom: 0;">
                <tr>
                    <td width="50%">
                        <table class="table" style="margin-bottom: 0">
                            <tbody>
                            <tr>
                                <td class="field">{{ trans('front.koc_group') }}:</td>
                                <td class="bottomline"></td>
                            </tr>
                            <tr>
                                <td class="field">{!! trans('front.time_period') !!}:</td>
                                <td class="bottomline">
                                    <b>From:</b> {{ Formatter::time()->human($report->getDateFrom()) }}
                                    <b>To:</b> {{ Formatter::time()->human($report->getDateTo()) }}
                                </td>
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
                    <th>{{ trans('front.allowed_speed_limit') . '(' . lcfirst(Formatter::speed()->unit()) . ')' }}</th>
                    <th>{{ trans('front.overspeed_duration_os') }}</th>
                    <th>{{ trans('front.overspeed_distance_os') . '(' . lcfirst(Formatter::distance()->unit()) . ')' }}</th>
                    <th>{{ trans('front.tamper_signs') }}</th>
                    <th>{{ trans('front.total_violations') }}</th>
                    <th>{{ trans('front.seatbelt_usage') }}</th>
                    <th>{{ trans('front.remark') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($report->getItems() as $item)
                    <?php $plate_number = $item['meta']['device.plate_number']['value'] ?? null; ?>
                    @if ( ! empty($item['table']))
                        @foreach ($item['table']['rows'] as $index => $row)
                            <tr>
                                <td>{{ $index }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>{{ $plate_number }}</td>
                                <td>{{ $report->getSpeedLimit() }}</td>
                                <td>{{ $row['duration'] }}</td>
                                <td>{{ $row['distance'] }}</td>
                                <td>{{ (is_null($row['gsm']) || $row['gsm'] > 5) ? 'false' : 'true' }}</td>
                                <td>{{ $item['totals']['overspeed_count']['value'] }}</td>
                                <td>{{ ($item['totals']['seatbelt_off_duration']['value'] != '0s') ? 'false' : 'true' }}</td>
                                <td>
                                    {{ ($item['totals']['harsh_acceleration_count']['value'] > 0) ? 'harsh_acceleration' : '' }}
                                    {{ ($item['totals']['harsh_breaking_count']['value'] > 0) ? 'harsh_breaking' : '' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">{{ $item['error'] }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>{{ trans('front.total') }}:</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $report->globalTotals('duration') }}</th>
                    <th>{{ $report->globalTotals('distance') }}</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@stop