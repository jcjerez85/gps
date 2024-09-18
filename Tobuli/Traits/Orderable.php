<?php
namespace Tobuli\Traits;

trait Orderable
{
    public function orderable_plan()
    {
        return $this->morphTo('plan', 'type', 'id');
    }

    public function calculateExpirationDate($startDate)
    {
        switch ($this->duration_type) {
            case 'days':
                $startDate->addDays($this->duration_value);
                break;
            case 'months':
                $startDate->addMonths($this->duration_value);
                break;
            case 'years':
                $startDate->addYears($this->duration_value);
                break;
        }

        return $startDate->toDateTimeString();
    }

    public function getDurationInDays()
    {
        return \Carbon\Carbon::parse($this->calculateExpirationDate(\Carbon\Carbon::today()))
            ->diffInDays();
    }
}
