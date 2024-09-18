<?php namespace Tobuli\Entities;

use Illuminate\Support\Arr;
use Tobuli\Entities\TraccarPosition as Position;

class DeviceSensor extends AbstractEntity {
    protected $table = 'device_sensors';

    protected $fillable = array(
        'user_id',
        'device_id',
        'name',
        'type',
        'tag_name',
        'add_to_history',
        'on_value',
        'off_value',
        'shown_value_by',
        'fuel_tank_name',
        'full_tank',
        'full_tank_value',
        'min_value',
        'max_value',
        'formula',
        'odometer_value_by',
        'odometer_value',
        'odometer_value_unit',
        'value',
        'value_formula',
        'show_in_popup',
        'unit_of_measurement',
        'on_tag_value',
        'off_tag_value',
        'on_type',
        'off_type',
        'calibrations',
        'skip_calibration',
        'skip_empty',
        'decbin',
        'hexbin'
    );

    public $timestamps = false;

    private $setflagfields = [];

    private $cacheCalibrations;

    public function user() {
        return $this->belongsTo('Tobuli\Entities\User', 'user_id', 'id');
    }

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'id', 'device_id');
    }

    public function getOdometerValueByAttribute($value)
    {
        if ($this->type == 'odometer' && TraccarPosition::VIRTUAL_ENGINE_HOURS_KEY == $this->tag_name)
            return 'virtual_odometer';

        return $value;
    }

    public function getTypeTitleAttribute($value)
    {
        if ( ! $this->type)
            return null;

        return config("tobuli.sensors.{$this->type}");
    }

    public function getOdometerValueAttribute($value)
    {
        if ($this->odometer_value_unit == 'mi')
            return kilometersToMiles($value);

        return $value;
    }

    public function getUnitOfMeasurementAttribute($value)
    {
        if ($this->type == 'gsm')
            $value = '%';
        elseif ($this->type == 'battery' && $this->shown_value_by == 'min_max_values')
            $value = '%';

        return $value;
    }

    public function setCalibrationsAttribute($value)
    {
        $this->attributes['calibrations'] = serialize($value);
    }

    public function getCalibrationsAttribute($value)
    {
        return unserialize($value);
    }

    public function getHashAttribute($value)
    {
        return md5($this->type . $this->name);
    }

    public function setValue($value)
    {
        if (is_array($value))
            return false;

        if (is_object($value))
            return false;

        $this->value = $value;

        if ($this->type == 'odometer' && $this->odometer_value_by == 'connected_odometer')
            $this->value_formula = $value;

        return true;
    }

    public function setCounter($value)
    {
        if (empty($this->value))
            $this->value = 0;

        $this->value += $value;
    }

    public function getCounter()
    {
        return $this->value ?? 0;
    }

    public function getSetflag($field)
    {
        if ( ! empty($this->setflagfields[$this->id]) && array_key_exists($field, $this->setflagfields[$this->id]))
            return $this->setflagfields[$this->id][$field];

        if (empty($this->{$field}))
            return $this->setflagfields[$this->id][$field] = null;

        $data = $field === 'formula'
            ? $this->getSetflagFormula($this->{$field})
            : $this->getSetflagValue($this->{$field});

        return $this->setflagfields[$this->id][$field] = ($data['values'] ? $data : null);
    }

    private function getSetflagFormula(string $fieldValue): array
    {
        $groups = $this->getGroupedPregMatch('/\%SETFLAG\[([0-9]+)\,([0-9]+)\]\%/', $fieldValue);
        $data = ['values' => []];

        foreach ($groups as $i => $group) {
            if (isset($group[1]) && isset($group[2])) {
                $data['values'][] = [
                    'index'   => $i,
                    'start'   => $group[1],
                    'count'   => $group[2],
                ];

                $fieldValue = str_replace($group[0], "[value{$i}]", $fieldValue);
            }
        }

        $data['formula'] = $fieldValue;

        return $data;
    }

    private function getSetflagValue(string $fieldValue): array
    {
        $groups = $this->getGroupedPregMatch('/\%SETFLAG\[([0-9]+)\,([0-9]+)\,([\s\S]+)\]\%/', $fieldValue);
        $data = ['values' => []];

        foreach ($groups as $group) {
            if (isset($group[1]) && isset($group[2]) && isset($group[3])) {
                $data['values'][] = [
                    'start'   => $group[1],
                    'count'   => $group[2],
                    'value'   => $group[3],
                ];
            }
        }

        return $data;
    }

    private function getGroupedPregMatch(string $pattern, string $fieldValue): array
    {
        preg_match_all($pattern, $fieldValue, $matches);

        $groups = [];

        foreach ($matches as $match) {
            foreach ($match as $i => $value) {
                $groups[$i][] = $value;
            }
        }

        return $groups;
    }

    public function getValueType()
    {
        switch ($this->type)
        {
            case 'gsm':
            case 'gps':
            case 'fuel_tank':
            case 'fuel_tank_calibration':
            case 'fuel_consumption':
            case 'odometer':
            case 'tachometer':
            case 'load':
            case 'load_calibration':
            case 'speed_ecm':
            case 'counter':
                return 'integer';
                break;

            case 'numerical':
            case 'battery':
            case 'temperature':
            case 'temperature_calibration':
                return 'float';
                break;

            case 'acc':
            case 'ignition':
            case 'engine':
            case 'door':
            case 'seatbelt':
            case 'drive_business':
            case 'drive_private':
            case 'route_color':
            case 'route_color_2':
            case 'route_color_3':
            case 'logical':
            case 'plugged':
            case 'harsh_acceleration':
            case 'harsh_breaking':
            case 'harsh_turning':
                return 'boolean';
                break;
            default:
                return null;
        }
    }

    public function isValid($value)
    {
        if (is_null($value))
            return false;

        if ($this->skip_empty && empty($value))
            return false;

        return true;
    }

    public function isCounter()
    {
        return $this->type == 'counter';
    }

    public function isPositionValue()
    {
        return $this->isBooleanValue() ||
            in_array($this->type, ['odometer', 'engine_hours', 'fuel_tank', 'fuel_tank_calibration', 'counter']);
    }

    public function isUpdatable()
    {
        return $this->isBooleanValue() || in_array($this->type, [
                'numerical',
                'textual',
                'odometer',
                'engine_hours',
                'fuel_tank',
                'fuel_tank_calibration',
                'fuel_consumption',
                'temperature',
                'temperature_calibration',
                'tachometer',
                'battery',
                'speed_ecm',
        ]);
    }

    public function timeoutValue()
    {
        switch ($this->type)
        {
            case 'gsm':
            case 'gps':
            case 'load':
            case 'load_calibration':
            case 'numerical':

            case 'battery':
            case 'tachometer':
            case 'temperature':
            case 'temperature_calibration':
            case 'speed_ecm':
                return 300;
                break;

            default:
                return null;
        }
    }

    public function isBooleanValue()
    {
        return 'boolean' == $this->getValueType();
    }

    public function isFloatValue()
    {
        return 'float' == $this->getValueType();
    }

    public function isIntegerValue()
    {
        return 'integer' == $this->getValueType();
    }

    public function getUnit()
    {
        if ($this->type == 'engine_hours'
            && ($this->tag_name == Position::VIRTUAL_ENGINE_HOURS_KEY || $this->shown_value_by == 'logical')
        )
            return trans('front.hour_short');

        return $this->unit_of_measurement;
    }

    public function formatValue($value)
    {
        if (is_null($value))
            return '-';

        if ($this->type == 'door')
            return $value ? trans('front.opened') : trans('front.closed');

        if ( $this->isBooleanValue() )
            return $value ? trans('front.on') : trans('front.off');

        if ($this->isFloatValue())
            $value = round($value, 2);

        if ($this->isIntegerValue())
            $value = round($value);

        $unit = $this->getUnit();

        return $value . ($unit ? ' ' . $unit : '');
    }

    public function formatName()
    {
        $description = '';

        if (in_array($this->type, ['fuel_tank', 'fuel_tank_calibration']) && !empty($this->fuel_tank_name))
            $description = '('.$this->fuel_tank_name.')';

        return htmlspecialchars($this->name . ($description ? ' ' . $description : ''));
    }

    public function getPercentage($other = null)
    {
        $percentage = 0;

        if ($this->type == 'fuel_tank' && $this->full_tank)
        {
            $percentage = floatval($this->getValue($other)) * 100 / $this->full_tank;
        }

        if ($this->type == 'fuel_tank_calibration')
        {
            $calibrations = $this->getCalibrations();

            if (!empty($calibrations['last_val']))
                $percentage = $this->getValue($other) * 100 / $calibrations['last_val'];
        }

        if ($this->type == 'gsm' || $this->type == 'battery')
        {
            $percentage = $this->getValue($other);
        }

        if ( $percentage < 0 )
            $percentage = 0;

        if ( $percentage > 100 )
            $percentage = 100;

        return round($percentage);
    }

    public function getValueScale($value)
    {
        if ($this->type == 'gsm' || $this->type == 'battery')
        {
            return ceil(($value ? floatval($value) : 0) / 20);
        }

        return null;
    }

    public function getValueCurrent($other = null)
    {
        $value = $this->isUpdatable() ? $this->value : null;

        $timeout = $this->timeoutValue();

        if( $timeout && time() - $this->updated_at > $timeout)
            $value = null;

        return $this->getValue($other, true, $value);
    }

    public function getValueFormated($other, $newest = true, $default = null) {
        $value = $this->getValue($other, $newest, $default);

        return $this->formatValue($value);
    }

    public function getValuePosition($position, $default = null)
    {
        $other = $position['other'];
        $value = $this->getValue($other, false);

        if (!is_null($value))
            return $value;

        $values = $position['sensors_values'];
        $values = is_string($values) ? json_decode($values, true) : $values;

        if (!is_array($values))
            return $default;

        $saved = Arr::where($values, function($value) { return $value['id'] == $this->id; });

        return $saved[0]['val'] ?? $default;
    }

    public function getValueParameters($other, $default = null) {
        $valueRaw = $this->getTagValue($other);

        if ( ! $this->isValid($valueRaw)) {
            return $default;
        }

        $value = $this->getValueRaw($this->type, $valueRaw);

        return is_null($value) ? $default : $value;
    }

    protected function getValueRaw($type, $valueRaw)
    {
        $sensor_value = null;

        switch ($type) {
            case 'harsh_breaking':
            case 'harsh_acceleration':
            case 'harsh_turning':
                if ($this->checkLogical($valueRaw, 'on_value', 1))
                    $sensor_value = true;
                break;

            case 'acc':
                if ($this->checkLogical($valueRaw, 'on_value', 1))
                    $sensor_value = true;

                if (is_null($sensor_value) && $this->checkLogical($valueRaw, 'off_value', 1))
                    $sensor_value = false;
                break;

            case 'door':
            case 'ignition':
            case 'engine':
            case 'seatbelt':
            case 'drive_business':
            case 'drive_private':
            case 'route_color':
            case 'route_color_2':
            case 'route_color_3':
            case 'logical':
            case 'plugged':
            case 'counter':
                if ($this->checkLogical($valueRaw, 'on_tag_value', $this->on_type))
                    $sensor_value = true;

                if (is_null($sensor_value) && $this->checkLogical($valueRaw, 'off_tag_value', $this->off_type))
                    $sensor_value = false;

                break;

            case 'battery':
                switch ($this->shown_value_by) {
                    case 'tag_value':
                        $sensor_value = parseNumber($valueRaw);
                        break;
                    case 'min_max_values':
                        $sensor_value = $this->getValueMinMax($valueRaw);
                        break;
                    case 'formula':
                        $sensor_value = $this->getValueFormula($valueRaw);
                        break;
                }
                break;

            case 'gsm':
                $sensor_value = $this->getValueMinMax($valueRaw);
                break;

            case 'odometer':
                switch ($this->odometer_value_by) {
                    case 'connected_odometer':
                        $sensor_value = $this->getValueFormula($valueRaw);
                        break;
                    case 'virtual_odometer':
                        $sensor_value = float($this->odometer_value);
                        break;
                }
                break;

            case 'fuel_tank':
                $value = $this->getValueFormula($valueRaw);

                if (is_numeric($this->full_tank) && is_numeric($this->full_tank_value) && is_numeric($value)) {
                    if ($this->full_tank != $this->full_tank_value)
                        $sensor_value = $this->full_tank * (getPrc($this->full_tank_value, $value) / 100);
                    else
                        $sensor_value = $value;
                }

                if (empty($sensor_value))
                    $sensor_value = null;
                break;

            case 'fuel_tank_calibration':
            case 'temperature_calibration':
            case 'load_calibration':
                $calibrations = $this->getCalibrations();

                $value = $this->getValueFormula($valueRaw);

                if (($value < $calibrations['first'] && $calibrations['order'] == 'dec') ||
                    ($value > $calibrations['first'] && $calibrations['order'] == 'asc'))
                {
                    $sensor_value = $this->skip_calibration ? null : $calibrations['first_val'];
                }
                else {
                    $prev_item = [];
                    foreach ($calibrations['calibrations'] as $x => $y) {
                        if (!empty($prev_item)) {
                            if (($value < $x && $calibrations['order'] == 'dec') ||
                                ($value > $x && $calibrations['order'] == 'asc'))
                            {
                                $sensor_value = calibrate($value, $prev_item['x'], $prev_item['y'], $x, $y);
                                break;
                            }
                        }
                        $prev_item = [
                            'x' => $x,
                            'y' => $y
                        ];
                    }

                    if ( ( ! $this->skip_calibration) && is_null($sensor_value))
                        $sensor_value = $y;
                }

                if ( ! is_null($sensor_value))
                    $sensor_value = round($sensor_value, 2);

                break;

            case 'speed_ecm':
            case 'temperature':
            case 'tachometer':
            case 'numerical':
            case 'load':
            case 'fuel_consumption':
                $sensor_value = $this->getValueFormula($valueRaw);
                break;

            case 'engine_hours':
                $sensor_value = $valueRaw;

                if ($this->tag_name == Position::VIRTUAL_ENGINE_HOURS_KEY)
                    $sensor_value = round($sensor_value / 3600, 4);

                break;
            case 'satellites':
                $sensor_value = $valueRaw;
                break;
            case 'textual':
            case 'rfid':
                $sensor_value = $this->convertDecHex($valueRaw);

                if ($setflag = $this->getSetflag('formula')) {
                    $setflag = array_first($setflag['values']);
                    $sensor_value = substr($sensor_value, $setflag['start'], $setflag['count']);
                }
                break;
        }

        return $sensor_value;
    }

    public function getValue($other, $newest = true, $default = null)
    {
        if ($this->type == 'odometer' && $this->odometer_value_by == 'virtual_odometer')
            return round($this->odometer_value, 3);

        if ($this->isCounter())
            return $this->value;

        $valueRaw = $this->getTagValue($other, $this->tag_name);

        if ( ! $this->isValid($valueRaw))
        {
            if ( ! $newest)
                return $default;

            if (is_null($this->value))
                return null;

            return $this->isBooleanValue() ? (bool)$this->value : $this->value;
        }

        $sensor_value = $this->getValueRaw($this->type, $valueRaw);

        return is_null($sensor_value) ? $default : $sensor_value;
    }

    protected function getTagValue($other, $tag_name = null)
    {
        if (is_null($tag_name))
            $tag_name = $this->tag_name;

        return parseTagValue($other, $tag_name);
    }

    protected function convertDecHex($value)
    {
        if ($this->decbin)
            $value = strrev(decbin(intval($value)));

        if ($this->hexbin) {
            try {
                $value = strrev(base_convert($value, 16, 2));
            } catch (\Exception $e) {
                $value = null;
            }
        }

        return $value;
    }

    protected function checkLogical($value, $field, $type)
    {
        $equal = $this->{$field};

        $value = $this->convertDecHex($value);

        if ( $setflag = $this->getSetflag($field) ) {
            $setflag = Arr::first($setflag['values']);
            $value = substr($value, $setflag['start'], $setflag['count']);
            $equal = $setflag['value'];
        }

        return checkCondition($type, $value, $equal);
    }

    protected function getValueFormula($value)
    {
        if (empty($this->formula) || $this->formula == '[value]')
            return parseNumber($value);

        $formula = $this->formula;
        $values = [];

        if ($setflag = $this->getSetflag('formula')) {
            $formula = $setflag['formula'];

            foreach ($setflag['values'] as $i => $item) {
                $values["[value{$item['index']}]"] = substr($value, $item['start'], $item['count']);
            }
        } else {
            $values['[value]'] = parseNumber($value);
        }

        return solveEquation($values, $formula);
    }

    protected function getValueMinMax($value)
    {
        $value_number = parseNumber($value);

        if (!(is_numeric($this->max_value) && is_numeric($this->min_value) && is_numeric($value_number)))
            return null;

        if ($value <= $this->min_value)
            return 0;

        if ($value >= $this->max_value)
            return 100;

        return getPrc($this->max_value - $this->min_value, ($value_number - $this->min_value));
    }

    public function getCalibrations()
    {
        if (!isset($this->cacheCalibrations))
        {
            $calibrations = $this->calibrations;
            krsort($calibrations);

            $calibrationsData = [
                'calibrations' => $calibrations,
                'first'        => key($calibrations),
                'first_val'    => current($calibrations),
                'last_val'     => end($calibrations),
                'last'         => key($calibrations),
                'order'        => 'asc'
            ];

            if ($calibrationsData['first_val'] > $calibrationsData['last_val'] &&
                $calibrationsData['first'] < $calibrationsData['last'])
            {
                $calibrationsData['order'] = 'dec';
            }

            $this->cacheCalibrations = $calibrationsData;
        }

        return $this->cacheCalibrations;
    }

    protected function sendSensorChange($value)
    {
        //if sensor value changed
        if ( $this->value == $value)
            return;

        $options = [
            'device'       => $this->device->imei,
            'sensor_id'    => $this->id,
            'sensor_type'  => $this->type,
            'sensor_value' => $this->value
        ];

        $this->sendSensorRemote($options);
    }

    public function getMaxTankValue()
    {
        if ($this->type == 'fuel_tank') {
            return $this->full_tank;
        }

        if ($this->type == 'fuel_tank_calibration') {
            $calibrations = $this->getCalibrations();

            return $calibrations['order'] = 'dec' ? $calibrations['first_val'] : $calibrations['last_val'];
        }

        return null;
    }
}
