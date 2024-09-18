@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
        <div class="panel panel-default">
            @include('Frontend.Reports.partials.item_heading')

            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('validation.attributes.date') }}</th>
                        <th>{{ trans('validation.attributes.name') }}</th>
                        <th>{{ trans('validation.attributes.type') }}</th>
                        <th>{{ trans('global.quantity') }}</th>
                        <th>{{ trans('validation.attributes.unit_cost') }}</th>
                        <th>{{ trans('front.total') }}</th>
                        <th>{{ trans('validation.attributes.supplier') }}</th>
                        <th>{{ trans('validation.attributes.buyer') }}</th>
                        <th>{{ trans('validation.attributes.additional_notes') }}</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if (empty($item['data']['expenses']))
                        <tr>
                            <td colspan="20">{{ trans('front.nothing_found_request') }}</td>
                        </tr>
                    @else
                        @foreach($item['data']['expenses'] as $expense)
                            <tr>
                                <td>{{ $expense['date'] }}</td>
                                <td>{{ $expense['name'] }}</td>
                                <td>{{ $expense['type']['name'] ?? null }}</td>
                                <td>{{ $expense['quantity'] }}</td>
                                <td>{{ $expense['unit_cost'] }}</td>
                                <td>{{ $expense['total'] }}</td>
                                <td>{{ $expense['supplier'] }}</td>
                                <td>{{ $expense['buyer'] }}</td>
                                <td>{{ $expense['additional'] }}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>{{ $item['data']['sum'] }}</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endforeach

@stop