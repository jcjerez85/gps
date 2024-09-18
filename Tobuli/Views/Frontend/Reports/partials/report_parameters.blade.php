<div class="panel panel-default">
    <div class="panel-body">
        <table class="table">
            <tbody>
                <tr>
                    <td>{{ trans('front.report_type') }}: {{ $report->title() }}</td>
                    <td>{{ trans('validation.attributes.period') }}: {{ Formatter::time()->human($report->getDateFrom()).' - '.Formatter::time()->human($report->getDateTo()) }}</td>
                </tr>
                <tr>
                    <td>{{ trans('front.selected_devices') }}: {{ implode(', ', $report->getDeviceNames()) }}</td>
                    <td>{{ trans('front.selected_geofences') }}: {{ implode(', ', $report->getGeofenceNames()) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
