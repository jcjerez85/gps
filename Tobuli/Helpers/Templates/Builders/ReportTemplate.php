<?php

namespace Tobuli\Helpers\Templates\Builders;

class ReportTemplate extends TemplateBuilder
{
    protected function variables($item)
    {
        return [
            '[name]'   => $item['title'],
            '[period]' => $item['date_from'] . ' - ' . $item['date_to'],
        ];
    }

    /**
     * @return array
     */
    protected function placeholders()
    {
        return [
            '[name]'   => 'Report title',
            '[period]' => 'Report date range',
        ];
    }
}