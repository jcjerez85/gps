<?php

namespace Tobuli\Services;

use Formatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tobuli\Entities\User;
use Tobuli\Exceptions\ValidationException;

class ScheduleService
{
    const BASE_NONE = 2;
    const BASE_UTC = 1;

    /**
     * @var int
     */
    private $base = self::BASE_UTC;

    /**
     * @var array
     */
    private $schedules;

    /**
     * @var array
     */
    private $times;

    /**
     * @var array
     */
    private $weekdays;

    /**
     * @var string[]
     */
    private $quarters;

    public function __construct(array $schedules = [])
    {
        $this->setSchedules($schedules);

        $this->times = getSelectTimeRange();
        $this->weekdays = getWeekdays();
        $this->quarters = ["00:00", "03:00", "06:00", "09:00", "12:00", "15:00", "18:00", "21:00"];
    }

    public function setBaseUtc(): self
    {
        $this->base = self::BASE_UTC;

        return $this;
    }

    public function setBaseNone(): self
    {
        $this->base = self::BASE_NONE;

        return $this;
    }

    public function setSchedules(array $schedules): self
    {
        $this->schedules = $this->checkFormatUpdate($schedules ?? []);

        return $this;
    }

    private function checkFormatUpdate(array $schedules): array
    {
        if (!is_string(Arr::first(Arr::first($schedules)))) {
            return $schedules;
        }

        $newSchedules = [];

        foreach ($schedules as $weekday => $times) {
            $dayIntervals = [];
            $iBegin = null;
            $iEnd = null;

            foreach ($times as $time) {
                $time = \Carbon::parse($time);

                if ($iBegin === null) {
                    $iBegin = $time;
                }

                if ($iEnd === null || $iEnd->diffInMinutes($time) === 15) {
                    $iEnd = $time;
                } else {
                    $dayIntervals[] = [$iBegin->format('H:i'), $iEnd->format('H:i')];
                    $iBegin = $time;
                    $iEnd = null;
                }
            }

            if ($iBegin && $iEnd) {
                $dayIntervals[] = [$iBegin->format('H:i'), $iEnd->format('H:i')];
            }

            $newSchedules[$weekday] = $dayIntervals;
        }

        return $newSchedules;
    }

    public function getFormSchedules(User $user = null): array
    {
        $schedules = [];

        $current = $this->convertSchedules($this->schedules, false);

        foreach ($this->weekdays as $weekday => $title) {
            $items = [];
            $actives = $current[$weekday] ?? [];

            foreach ($this->times as $time => $displayTime) {
                $items[] = [
                    'id' => $time,
                    'title' => $displayTime,
                    'active' => $this->isTimeInPeriods($time, $actives),
                    'class' =>
                        (Str::endsWith($time, ":00") ? ' hour' : '') .
                        (in_array($time, $this->quarters) ? ' quarter' : '')
                ];
            }

            $schedules[] = [
                'id' => $weekday,
                'title' => $title,
                'items' => $items,
            ];
        }

        $invert = $user ? (8 - $user->week_start_day) % 7 : 0;

        while ($invert-- > 0) {
            array_unshift($schedules, array_pop($schedules));
        }

        return $schedules;
    }

    public function setFormSchedules(array $schedules): array
    {
        $schedules = $this->checkFormatUpdate($schedules);

        return $this->convertSchedules($schedules, true);
    }

    /**
     * @throws ValidationException
     */
    public function validate(array $input, string $prefix = 'schedules')
    {
        if (!$input) {
            return;
        }

        foreach ($input as $weekday => $schedule) {
            if (!array_key_exists($weekday, $this->weekdays))
                throw new ValidationException(["$prefix.$weekday" => 'Wrong week day.']);

            $validator = Validator::make([$prefix => $input], [
                "$prefix.$weekday"   => 'required|array',
                "$prefix.$weekday.*" => 'in:' . implode(',' ,array_keys($this->times))
            ]);

            if ($validator->fails()) {
                throw new ValidationException([$prefix => $validator->errors()->first()]);
            }
        }
    }

    public function outSchedules(string $date): bool
    {
        return !$this->inSchedules($date);
    }

    public function inSchedules(string $date): bool
    {
        if (empty($this->schedules)) {
            return false;
        }

        list($_weekday, $_time) = $this->splitDate($date);

        return $this->isTimeInPeriods($_time, $this->schedules[$_weekday] ?? []);
    }

    private function isTimeInPeriods(string $time, array $periods): bool
    {
        foreach ($periods as $times) {
            if ($time >= $times[0] && $time <= $times[1]) {
                return true;
            }
        }

        return false;
    }

    public function closestScheduleTime(string $date): string
    {
        $closestSchedule = $this->closestSchedule($date);

        if (!$closestSchedule)
            return '-';

        $time = strtotime($closestSchedule[0] . ' ' . $closestSchedule[1]);

        $time = date('Y-m-d H:i:s', $time);

        return Formatter::time()->human($time);
    }

    /**
     * @return array|null
     */
    public function closestSchedule(string $date)
    {
        if (empty($this->schedules))
            return null;

        list($weekday, $time) = $this->splitDate($date);

        //sort weekdays list while required weekday first
        $weekdays = array_keys($this->weekdays);
        while ($weekdays[0] != $weekday) {
            $weekdays[] = array_shift($weekdays);
        }

        foreach ($weekdays as $_weekday) {
            if (empty($this->schedules[$_weekday]))
                continue;

            if ($weekday != $_weekday) {
                return [
                    $_weekday,
                    $this->schedules[$_weekday][0][0],
                ];
            }

            foreach ($this->schedules[$_weekday] as $_times) {
                if ($_times[0] >= $time) {
                    return [
                        $_weekday,
                        $_times[0],
                    ];
                }
            }
        }

        return null;
    }

    private function convertSchedules(array $schedules, bool $reverse): array
    {
        if (empty($schedules))
            return [];

        if ($this->base === self::BASE_NONE)
            return $schedules;

        if (Formatter::DST()->isUTC())
            return $schedules;

        $result = [];

        foreach($schedules as $weekday => $periods) {
            foreach ($periods as $period) {
                list($_weekdayStart, $_timeStart) = $this->convertDate($weekday, $period[0], $reverse);
                list($_weekdayEnd, $_timeEnd) = $this->convertDate($weekday, $period[1], $reverse);

                if ($_weekdayStart === $_weekdayEnd) {
                    $result[$_weekdayStart][] = [$_timeStart, $_timeEnd];
                } else {
                    $result[$_weekdayStart][] = [$_timeStart, '23:45'];
                    $result[$_weekdayEnd][] = ['00:00', $_timeEnd];
                }
            }
        }

        return $result;
    }

    private function convertDate($weekday, $time, bool $reverse = true): array
    {
        $_time = strtotime($weekday . ' ' . $time);

        if ($reverse) {
            $_time = Formatter::time()->reverse(date('Y-m-d H:i:s', $_time), 'l H:i');
        } else {
            $_time = Formatter::time()->convert(date('Y-m-d H:i:s', $_time), 'l H:i');
        }

        list($_weekday, $_time) = explode(' ', $_time);

        $_weekday = strtolower($_weekday);

        return [$_weekday, $_time];
    }

    private function splitDate(string $date): array
    {
        $_time = $this->roundHourMinutesTo($date);
        $_time = strtotime($_time);
        $_time = date('l H:i', $_time);

        list($_weekday, $_time) = explode(' ', $_time);

        return [
            strtolower($_weekday),
            $_time
        ];
    }

    private function roundHourMinutesTo(string $timestring, int $round = 15): string
    {
        try {
            $time = \Carbon\Carbon::createMidnightDate()->parse($timestring);
        } catch (\Exception $e) {
            $time = \Carbon\Carbon::createMidnightDate();
        }

        $minutes = date('i', strtotime($timestring));

        if ($sub = $minutes % $round)
            $time->subMinutes($sub);

        return $time->format('H:i');
    }
}