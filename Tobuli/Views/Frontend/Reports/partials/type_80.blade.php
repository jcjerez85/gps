@extends('front::Reports.partials.layout')

@section('content')
    @php $items = $report->getItems() @endphp

    @foreach ($report->getGeofences()->pluck('name', 'id') as $id => $geofenceName)
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th class="col-sm-3">{{ trans('front.geofence') }}:</th>
                            <td class="col-sm-3">{{ $geofenceName }}</td>
                            <th class="col-sm-3">&nbsp;</th>
                            <td class="col-sm-3">&nbsp;</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($item = $items[$id] ?? null)
                <div class="panel-body no-padding">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            @foreach ($report->metas() as $meta)
                                <th>{{ $meta['title'] }}</th>
                            @endforeach
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($item['devices'] as $device)
                            <tr>
                                @foreach ($report->metas() as $key => $meta)
                                    <td>{{ $device[$key]['value'] }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="panel-body">
                    <span>{{ trans('front.nothing_found_request') }}</span>
                </div>
            @endif
        </div>
    @endforeach
@stop