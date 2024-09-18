@extends('Frontend.Reports.partials.layout')

@section('content')
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_heading')

        <div class="panel-body no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>{{ trans('validation.attributes.installation_date') }}</th>
                    @foreach($report->metas('device') as $meta)
                        <th>{{ $meta['title'] }}</th>
                    @endforeach
                    <th>{{ trans('validation.attributes.sim_activation_date') }}</th>
                    <th>{{ trans('validation.attributes.sim_expiration_date') }}</th>
                </tr>
                </thead>
                <tbody>
                @if (empty($report->getItems()))
                    <tr>
                        <td colspan="{{ count($report->metas('device')) + 3 }}">{{ trans('front.nothing_found_request') }}</td>
                    </tr>
                @else
                @foreach ($report->getItems() as $item)
                    <tr>
                        <td>{{ $item['data']['installation_date'] }}</td>
                        @foreach($item['meta'] as $key => $meta)
                        <td>{{ $meta['value'] }}</td>
                        @endforeach
                        <td>{{ $item['data']['sim_activation_date'] }}</td>
                        <td>{{ $item['data']['sim_expiration_date'] }}</td>
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@stop