<?php namespace Tobuli\Entities;


class TraccarPosition extends AbstractEntity {
    const VIRTUAL_ENGINE_HOURS_KEY = 'enginehours';
    const ENGINE_HOURS_KEY = 'hours';

    protected $connection = 'traccar_mysql';

	protected $table = 'positions';

    protected $fillable = [
        'altitude',
        'course',
        'latitude',
        'longitude',
        'other',
        'power',
        'speed',
        'time',
        'device_time',
        'server_time',
        'valid',
        'protocol',
        'distance',

        'sensors_values',
        'parameters',
    ];

    public $timestamps = false;

    public function newInstance($attributes = [], $exists = false)
    {
        $model = new static((array) $attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        $model->setTable(
            $this->getTable()
        );

        return $model;
    }

    public function device() {
        return $this->hasOne('Tobuli\Entities\Device', 'traccar_device_id', 'device_id');
    }

    public function scopeLastest($query)
    {
        return $query->orderBy('time', 'desc');
    }

    public function scopeOrderliness($query, $order = 'desc')
    {
        return $query->orderBy('time', $order)->orderBy('id', $order);
    }

    public function getSpeedAttribute($value)
    {
        return float($value);
    }

    public function getParameter($key, $default = null)
    {
        $parameters = $this->parameters;

        return array_key_exists($key, $parameters) ? $this->parameters[$key] : $default;
    }

    public function setParameter($key, $value)
    {
        $parameters = $this->parameters;

        $parameters[$key] = $value;

        $this->parameters = $parameters;
    }

    public function hasParameter($key)
    {
        $parameters = $this->parameters;

        return array_key_exists($key, $parameters);
    }

    public function setParametersAttribute($value)
    {
        if ( is_array($value))
        {
            $xml = '<info>';

            foreach ($value as $key => $val)
            {
                if (is_numeric($key)) continue;
                if (is_array($val)) continue;

                $val = is_bool($val) ? ($val ? 'true' : 'false') : $val;
                $val = html_entity_decode($val);
                $xml .= "<{$key}>{$val}</$key>";
            }
            $xml .= '</info>';

            $value = $xml;
        }

        $this->attributes['other'] = $value;
    }

    public function getParametersAttribute()
    {
        if (empty($this->attributes['other']))
            return [];

        $value = $this->attributes['other'];

        return parseXMLToArray($value);
    }

    public function isRfid($rfid)
    {
        if (empty($rfid))
            return false;

        switch ($this->protocol)
        {
            case 'teltonika':
                return $rfid == $this->rfid || $rfid == $this->rfidRaw;
                break;
            default:
                return $rfid == $this->rfid;
        }
    }

    public function getRfids()
    {
        $rfids = [];

        if ( ! $this->rfidRaw)
            return $rfids;

        switch ($this->protocol)
        {
            case 'teltonika':
                $rfids[] = $this->rfid;
                $rfids[] = $this->rfidRaw;
                break;
            case 'meitrack':
                $rfids[] = $this->rfid;
                $rfids[] = $this->rfidRaw;
                break;
            default:
                $rfids[] = $this->rfid;
                break;
        }

        return $rfids;
    }

    public function getRfidAttribute()
    {
        $rfid = $this->getRfidRawAttribute();

        if ($rfid) {
            try {
                switch ($this->protocol) {
                    case 'teltonika':
                        $rfid = teltonikaIbutton($rfid);
                        break;
                    case 'meitrack':
                        $rfid = hexdec($rfid);
                        break;
                }
            } catch (\Exception $e) {}
        }

        return (string)$rfid;
    }

    public function getRfidRawAttribute()
    {
        $parameters = $this->parameters;

        $rfid = empty($parameters['rfid']) ? null : $parameters['rfid'];

        if ( ! $rfid)
        {
            switch ($this->protocol)
            {
                case 'teltonika':
                    $rfid = empty($parameters['io78']) ? null : $parameters['io78'];
                    break;
                case 'fox':
                    $rfid = empty($parameters['status-data']) ? null : $parameters['status-data'];
                    break;
                case 'ruptela':
                    $rfid = empty($parameters['io34']) ? null : $parameters['io34'];
                    $rfid = (is_null($rfid) && ! empty($parameters['io171'])) ? $parameters['io171'] : $rfid;
                    break;
            }
        }

        if ( ! $rfid && ! empty($parameters['driveruniqueid']))
            $rfid = $parameters['driveruniqueid'];

        if ( ! $rfid && ! empty($parameters['driver1']))
            $rfid = $parameters['driver1'];

        if ( ! $rfid && ! empty($parameters['beacon1uuid']))
            $rfid = $parameters['beacon1uuid'];

        return $rfid;
    }

    public function getSensorsValuesAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setSensorsValuesAttribute($value)
    {
        $this->attributes['sensors_values'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getSensorValue($sensor_id)
    {
        $sensors = $this->sensors_values;

        if (empty($sensors))
            return null;

        if ( ! is_array($sensors))
            return null;

        foreach ($sensors as $sensor)
        {
            if ($sensor['id'] == $sensor_id)
                return $sensor['val'];
        }

        return null;
    }

    public function isValid()
    {
        return $this->valid > 0 ? true : false;
    }

    public function getVirtualEngineHours()
    {
        return $this->getParameter(self::VIRTUAL_ENGINE_HOURS_KEY, 0);
    }

    public function getEngineHours()
    {
        return floatval($this->getParameter(self::ENGINE_HOURS_KEY, 0)) * 3600;
    }
}
