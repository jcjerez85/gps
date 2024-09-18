@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('Frontend.Reports.partials.item_empty')
            @else

                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('front.time') }}</th>
                                <th>{{ trans('front.last_value') }}</th>
                                <th>{{ trans('front.difference') }}</th>
                                <th>{{ trans('front.current_value') }}</th>
                                <th>{{ trans('front.position') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table']['rows'] as $row)
                                <tr>
                                    <td>{{ $row['start_at'] }}</td>
                                    <td>{{ $row['fuel_level_previous'] }}</td>
                                    <td>{{ $row['fuel_level_difference'] }}</td>
                                    <td>{{ $row['fuel_level_current'] }}</td>
                                    <td >{!! $row['location'] !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @include('Frontend.Reports.partials.item_total')
            @endif
        </div>
    @endforeach
@stop