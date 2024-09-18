<?php
    if (! isset($emptyItems)) {
        $emptyItems = array_filter($report->getItems(),
            function($item) {
                return isset($item['error']);
            });
    }
?>

@if (empty($report->getItems()) || count($emptyItems) == count($report->getItems()))
    @include('Frontend.Reports.partials.report_parameters')
@endif
