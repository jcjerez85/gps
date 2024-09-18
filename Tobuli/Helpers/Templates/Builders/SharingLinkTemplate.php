<?php

namespace Tobuli\Helpers\Templates\Builders;

use Tobuli\Entities\Sharing;

class SharingLinkTemplate extends TemplateBuilder
{
    /**
     * @param Sharing $sharing
     * @return array
     */
    protected function variables($sharing)
    {
        return [
            '[link]' => $sharing->link,
        ];
    }

    protected function placeholders()
    {
        return [
            '[link]' => 'Share link',
        ];
    }
}