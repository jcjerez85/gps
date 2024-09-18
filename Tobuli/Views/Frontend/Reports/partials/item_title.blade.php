<div class="panel-heading">
    <div class="pull-right">{{ Formatter::time()->human($report->getDateFrom()) }} - {{ Formatter::time()->human($report->getDateTo()) }} ({{ Formatter::time()->unit() }})</div>
    <div class="report-bars"></div>
    {{ trans('front.report_type') }}: {{ $report->title() }}
</div>