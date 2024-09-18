@extends('Frontend.Reports.partials.layout')

@section('content')
    @foreach ($report->getItems() as $item)
    <div class="panel panel-default">
        @include('Frontend.Reports.partials.item_title')

        @if ( ! empty($item['meta']))
        <div class="panel-body">
            <table class="table">
                <tbody>
                @foreach($item['meta'] as $key => $meta)
                    <tr>
                        <th class="col-sm-3">{{ $meta['title'] }}:</th>
                        <td class="col-sm-3" style="{{ $key == 'device.name' ? 'font-weight: 600;font-size: 15px;' : '' }}">{{ $meta['value'] }}</td>
                        <th class="col-sm-3">&nbsp;</th>
                        <td class="col-sm-3">&nbsp;</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if (! empty($item['data']))
            @foreach ($item['data'] as $data)
            <div class="panel-body no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th colspan="3">{{ trans('front.service').': '.$data['service']->name }}</th>
                        </tr>
                        <tr>
                            <th>{{ trans('validation.attributes.status') }}</th>
                            <th>{{ trans('front.outcome') }}</th>
                            <th>{{ trans('front.activity') }}</th>
                            <th>{{ trans('front.time_completed') }}</th>
                        </tr>
                    </thead>

                    @foreach ($data['checklists'] as $checklist)
                    <tbody>
                        <tr>
                            <td colspan="4" style="text-align: left;">
                                <div class="panel-body">
                                    <div>{{ trans('front.checklist').': '.$checklist->name }}</div>
                                    <div>{{ trans('front.time_completed').': '.($checklist->completed_at ? Formatter::time()->human($checklist->completed_at) : trans('front.incomplete')) }}</div>
                                    <div>{{ trans('validation.attributes.signature').': '.$checklist->signature }}</div>
                                    <div><strong>{{ trans('validation.attributes.notes').': '.$checklist->notes }}</strong></div>
                                </div>
                            </td>
                        </tr>
                        @foreach ($checklist->rows as $row)
                            <tr>
                                <td>{{ $row->completed ? trans('front.complete') : trans('front.incomplete') }}</td>
                                <td>{{ $row->formatted_outcome }}</td>
                                <td>{{ $row->activity }}</td>
                                <td>{{ $row->completed_at ? Formatter::time()->human($row->completed_at) : '-' }}</td>
                            </tr>
                            @if (!$row->images->isEmpty())
                            <tr>
                                <td colspan="4">
                                    @foreach ($row->images as $image)
                                        <img src="{{ url($image->path) }}" style="max-width: 250px;" />
                                    @endforeach
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    @endforeach
                </table>
            </div>
            @endforeach
        @endif
    </div>
    @endforeach
@stop
