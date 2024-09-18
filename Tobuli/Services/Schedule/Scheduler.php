<?php namespace Tobuli\Services\Schedule;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Tobuli\Entities\Schedule;
use Tobuli\Exceptions\ValidationException;
use Formatter;

class Scheduler
{

    /**
     * @param $subject
     * @param $data
     * @param $user
     */
    public function create($subject, $data, $user)
    {
        $type = $data['schedule_type'];
        $parameters = $data[$type];

        $schedule = new Schedule([
            'type'    => $type,
            'user_id' => $user->id
        ]);

        $schedule->fill($this->getData($schedule, $parameters));

        $subject->schedule()->save($schedule);
    }

    /**
     * @param $subject
     * @param $data
     */
    public function update($subject, $data)
    {
        $type = $data['schedule_type'];
        $parameters = $data[$type];

        $subject->schedule->type = $type;
        $subject->schedule->fill($this->getData($subject->schedule, $parameters));
        $subject->schedule->update();
    }

    /**
     * Reschedules event if necessary.
     *
     * @param Schedule $schedule
     */
    public function reschedule(Schedule $schedule)
    {
        $schedule->update(['last_run_at' => Carbon::now()]);

        /* Save exact time schedule last run. */
        if ($schedule->type == Schedule::TYPE_EXACT_TIME) {
            return;
        }

        $schedule->update($this->getData($schedule, $schedule->parameters));
    }

    /**
     * Sets next schedule date and persists.
     *
     * @param $data
     * @return mixed
     */
    private function getData(Schedule $schedule, $data)
    {
        Formatter::byUser($schedule->user);

        return call_user_func_array([$this, Str::camel($schedule->type)], [$data]);
    }

    /**
     * Schedule tasks at exact time.
     *
     * @param $schedule
     * @param $data
     * @return mixed
     */
    private function exactTime($data)
    {
        $schedule_at = Formatter::time()->reverse($data['time']);

        if (strtotime($schedule_at) <= time()) {
            throw new ValidationException(trans('validation.after', [
                'attribute' => trans('front.time'),
                'date'      => 'now',
            ]));
        }

        return [
            'schedule_at' => $schedule_at
        ];
    }

    /**
     * Schedule houly tasks.
     *
     * @param $data
     * @return array
     */
    private function hourly($data)
    {
        $schedule_at = Carbon::now()->startOfHour()->addMinutes($data['minute']);

        if (($schedule_at->timestamp - time()) < 0) {
            $schedule_at = $schedule_at->addHour();
        }

        return [
            'schedule_at' => $schedule_at->toDateTimeString(),
            'parameters'  => ['minute' => $data['minute']]
        ];
    }

    /**
     * Schedule daily tasks.
     *
     * @param $data
     * @return array
     */
    private function daily($data)
    {
        $reverse_at = Formatter::time()->reverse($data['time']);

        $schedule_at = date('Y-m-d H:i:s', strtotime("Tomorrow $reverse_at"));

        if (strtotime($reverse_at) > time()) {
            $schedule_at = $reverse_at;
        }

        return [
            'schedule_at' => $schedule_at,
            'parameters'  => ['time' => $data['time']]
        ];
    }

    /**
     * Schedule weekly tasks.
     *
     * @param $data
     * @return mixed
     * @throws ValidationException
     */
    private function weekly($data)
    {
        $parameters = [];
        $seconds_to_schedule = INF;

        foreach ($data['days'] as $weekday => $options) {
            if (empty($options['checked'])) {
                continue;
            }

            $date = Formatter::time()->reverse("{$weekday} {$options['time']}");
            $time = Carbon::parse($date);

            if (empty($parameters)) {
                $schedule_at = $time;
            }

            $parameters['days'][$weekday]['time'] = $options['time'];
            $parameters['days'][$weekday]['checked'] = true;

            $difference = $time->timestamp - Formatter::time()->now();

            if ($difference < 0) {
                $time->addWeek();
                $schedule_at = $time;
                $seconds_to_schedule = $time->timestamp - Formatter::time()->now();

                continue;
            }

            if ($difference > $seconds_to_schedule) {
                continue;
            }

            $schedule_at = $time;
            $seconds_to_schedule = $difference;
        }

        if (empty($parameters)) {
            throw new ValidationException(trans('validation.required', ['attribute' => trans('front.weekday')]));
        }

        return [
            'schedule_at' => $schedule_at->toDateTimeString(),
            'parameters'  => $parameters
        ];
    }

    /**
     * Schedule monthly tasks.
     *
     * @param $data
     * @return mixed
     */
    private function monthly($data)
    {
        $month = Formatter::time()->convert(date('Y-m-d H:i:s'), 'F');
        $time_string = "{$month} {$data['day']} {$data['time']}";

        $i = 1;

        while (Formatter::time()->convert($time_string, 'F') != $month) {
            $day = $data['day'] - $i;
            $time_string = "{$month} {$day} {$data['time']}";
        }

        if ((strtotime(Formatter::time()->timestamp($time_string)) - Formatter::time()->now()) < 0) {
            $time_string .= ' +1 month';
        }

        return [
            'schedule_at' => Formatter::time()->reverse($time_string),
            'parameters'  => [
                'day' => $data['day'],
                'time' => $data['time']
            ]
        ];
    }

}