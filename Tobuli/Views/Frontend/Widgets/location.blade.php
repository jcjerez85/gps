<div id="widget-location" class="widget widget-device">
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon address"></i> {{ trans('front.location') }}
        </div>
    </div>

    <div class="widget-body">
        <table class="table">
            <tbody>
            <tr>
                <td>{{ trans('front.city') }}:</td>
                <td>{{ $location['city'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.road') }}:</td>
                <td>{{ $location['road'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.house') }}:</td>
                <td>{{ $location['house'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.zip') }}:</td>
                <td>{{ $location['zip'] ?? '-' }}</td>
            </tr>
            </tbody>
        </table>
        <table class="table">
            <tbody>
            <tr>
                <td>{{ trans('front.country') }}:</td>
                <td>{{ $location['country'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.county') }}:</td>
                <td>{{ $location['county'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.state') }}:</td>
                <td>{{ $location['state'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ trans('front.address') }}:</td>
                <td>
                    @if ( ! (empty($location['lat']) && empty($location['lng'])))
                        <span class="pull-right p-relative">
                        <a href="//maps.google.com/maps?q={{ $location['lat'] }},{{ $location['lng'] }}&amp;t=m&amp;hl=en" target="_blank" class="btn btn-xs btn-default"><i class="icon eye"></i></a>
                    </span>
                    @endif
                    {{ $location['address'] ?? '-' }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>