<?php namespace Tobuli\Helpers\Dashboard\Blocks;


use CustomFacades\ModalHelpers\AlertModalHelper;
use Tobuli\Helpers\Dashboard\Traits\HasPeriodOption;

class LatestEventsBlock extends Block
{
    use HasPeriodOption;

    protected function getName()
    {
        return 'latest_events';
    }

    protected function getContent()
    {
        $alerts = AlertModalHelper::summary($this->getPeriod());

        return ['events' => $alerts->sortByDesc('count')->all()];
    }

}