@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('front::Reports.partials.item_heading')

            @if (isset($item['error']))
                @include('front::Reports.partials.item_empty')
            @else
                @if ( ! empty($item['table']))
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans('validation.attributes.date') }}</th>
                                <th>{{ trans('front.start') }}</th>
                                <th>{{ trans('front.end') }}</th>
                                <th>{{ trans('global.distance') }}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($item['table'] as $row)
                                <tr>
                                    <td>{{ $row['date'] }}</td>
                                    <td>{{ $row['odometer_start'] }}</td>
                                    <td>{{ $row['odometer_end'] }}</td>
                                    <td>{{ $row['distance'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>

                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>{{ trans('front.total') }}</td>
                                <td>{{ $item['totals']['distance'] }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    @endforeach
@stop