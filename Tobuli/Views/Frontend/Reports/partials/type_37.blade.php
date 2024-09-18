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
                                <th>{{ trans('front.loading') }}</th>
                                <th>{{ trans('front.unloading') }}</th>
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
                                    <td>{{ $row['state'] === 1 ? trans('front.loading') : '' }}</td>
                                    <td>{{ $row['state'] === 0 ? trans('front.unloading') : '' }}</td>
                                    <td>{{ $row['time'] }}</td>
                                    <td>{{ $row['previous_load'] }}</td>
                                    <td>{{ $row['difference'] }}</td>
                                    <td>{{ $row['current_load'] }}</td>
                                    <td>{!! $row['location'] !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if( ! empty($item['totals']))
                    <div class="panel-body">
                        @include('Frontend.Reports.partials.item_total_table', ['totals' => $item['totals'], 'list' => ['loading_count', 'unloading_count']])
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop