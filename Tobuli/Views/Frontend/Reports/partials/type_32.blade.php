@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        @if ( ! empty($item['data']['commands']))
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            <div class="panel-body no-padding">
                <table class="table table-hover" style="margin-bottom: 0px">
                    <thead>
                    <tr>
                        <th>{{ trans('front.time') }}</th>
                        @if($report->globalTotals('is_manager'))
                            <th>{{ trans('global.user') }}</th>
                        @endif
                        <th>{{ trans('front.connection') }}</th>
                        <th>{{ trans('front.command') }}</th>
                        <th>{{ trans('validation.attributes.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if (empty($item['data']['commands']))
                        <tr>
                            <td colspan="5">{{ trans('front.nothing_found_request') }}</td>
                        </tr>
                    @else
                        @foreach ($item['data']['commands'] as $item)
                            <tr>
                                <td>{{ $item['time'] }}</td>
                                @if($report->globalTotals('is_manager'))
                                    <td>{{ $item['email'] }}</td>
                                @endif
                                <td>{{ $item['connection'] }}</td>
                                <td>{{ $item['command'] }}</td>
                                <td>{{ $item['status'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
@stop