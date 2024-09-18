@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        @if ( ! empty($item['table']))
            <div class="panel-body no-padding">
                @foreach ($item['table']['rows'] as $row)
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="8">{{ trans('validation.attributes.date') }}</th>
                        <th colspan="20">{{ trans('front.start') }}</th>
                        <th colspan="20">{{ trans('front.stop') }}</th>
                        <th colspan="12">{{ trans('front.duration') }}</th>
                        <th colspan="12">{{ trans('front.engine_hours') }}</th>
                        <th colspan="12">{{ trans('global.distance') }}</th>
                        <th colspan="12">{{ trans('front.move_duration') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="8">{{ $row['date'] ?? null }}</td>
                            <td colspan="20">
                                {{ $row['first_engine_time'] ?? null }}
                                <br>
                                {{ $row['first_engine_address'] ?? null }}
                            </td>
                            <td colspan="20">
                                {{ $row['last_engine_time'] ?? null }}
                                <br>
                                {{ $row['last_engine_address'] ?? null }}
                            </td>
                            <td colspan="12">{{ $row['duration'] ?? null }}</td>
                            <td colspan="12">{{ $row['engine_hours'] ?? null }}</td>
                            <td colspan="12">{{ $row['drive_distance'] ?? null }}</td>
                            <td colspan="12">{{ $row['drive_duration'] ?? null }}</td>
                        </tr>
                        <tr>
                            @for ($i = 0; $i < 24; $i++)
                                <th colspan="4" style="width: 4.1666%">{{ $i }}</th>
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = 0; $i < 24; $i++)
                                <td colspan="4">{{ empty($row['hourly'][$i]) ? null : round($row['hourly'][$i]) }}</td>
                            @endfor
                        </tr>
                        <tr>
                            @for ($i = 0; $i < 1440; $i += 15)
                            <td style="height: 40px; width: 1.0417%; padding: 0; vertical-align: bottom">
                                @if (!empty($row['quarter'][$i]))
                                <div style="background: #1A99BC; height: {{ $row['quarter'][$i] }}%"></div>
                                @endif
                            </td>
                            @endfor
                        </tr>

                    </tbody>
                </table>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach
@stop