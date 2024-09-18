<?php namespace ModalHelpers;

use CustomFacades\ModalHelpers\HistoryModalHelper;
use CustomFacades\Validators\HistoryFormValidator;
use Formatter;
use Tobuli\Exceptions\ValidationException;

class HistoryExportModalHelper extends ModalHelper {
	
	public static $base_path;

	protected $device;
	
	public function __construct()
	{
		parent::__construct();
		
        self::$base_path = storage_path('logs/');
    }

	public function get()
	{
        HistoryFormValidator::validate('create', $this->data);

        $format	= request()->get('format', '');
        $this->device = HistoryModalHelper::getDevice();
        $this->dateFrom = Formatter::time()->reverse(request()->get('from_date', '').' '.request()->get('from_time', ''));
        $this->dateTo	= Formatter::time()->reverse(request()->get('to_date', '').' '.request()->get('to_time', ''));

        $data = HistoryModalHelper::getHistoryData($this->device);

        if ( ! $data['root']->getStartPosition() ) {
            return [ 'error' => trans('front.no_history') ];
        }

        $file = md5( serialize(request()->all()) );
        $fp	  = fopen( self::$base_path . $file, 'w' );
		
        switch ( $format )
        {
            case 'gsr':
                $this->generateGsrFile($data, $fp);
                break;

            case 'kml':
                $this->generateKmlFile($data, $fp);
                break;

            case 'gpx':
                $this->generateGpxFile($data, $fp);
                break;

            case 'csv':
                $this->generateCsvFile($data, $fp);
                break;

            default:
                break;
        }
			
        fclose( $fp );

        return [
            'download' => route('history.download', [$file, urlencode($this->getFilename($format))])
        ];
	}

    public function getFile( $file ) {
        $file = str_replace('..', '', $file);

        return self::$base_path . $file;
    }

	private function getFilename($extension)
    {
        $date_from	= request()->get('from_date', '').' '.request()->get('from_time', '');
        $date_to	= request()->get('to_date', '').' '.request()->get('to_time', '');
        $filename	= $this->device->name . ' ' . str_replace( ':', '-', $date_from . ' - ' . $date_to );
        $filename	= str_replace( ['../','/',' '], ['','-','_'], $filename );

        return $filename . '.' . $extension;
    }

    private function generateGsrFile($data, $fp)
    {
        $json = [
            'gsr' => '0.2v',
            'imei' => $this->device->imei,
            'name' => $this->device->name,
            'route' => [],

            'route_length' => $data['root']->getStat('distance')->human(),
            'top_speed' => $data['root']->getStat('speed_max')->human(),
            'avg_speed' => $data['root']->getStat('speed_avg')->human(),
            'fuel_consumption' => $data['root']->hasStat('fuel_consumption') ? $data['root']->getStat('fuel_consumption')->human() : 0,
            'fuel_cost' => $data['root']->hasStat('fuel_price') ? $data['root']->getStat('fuel_price')->human() : 0,
            'stops_duration' => $data['root']->getStat('stop_duration')->human(),
            'drives_duration' => $data['root']->getStat('drive_duration')->human(),
            'engine_work' => $data['root']->getStat('engine_work')->human(),
            'engine_idle' => $data['root']->getStat('engine_idle')->human(),
        ];

        foreach ($data['groups']->all() as $group) {
            $positions = $group->getStat('positions')->value();

            foreach ($positions as $position) {
                $json['route']['route'][] = [
                    Formatter::time()->convert($position->time),
                    $position->latitude,
                    $position->longitude,
                    Formatter::altitude()->format($position->altitude),
                    $position->course,
                    Formatter::speed()->format($position->speed),
                ];
            }

            switch ($group->getKey()) {
                case 'stop':
                    $lastStopPosition = $group->getEndPosition();

                    $json['route']['stops'][] = [
                        $group->getStartPosition()->index,  //first stop cord index
                        $lastStopPosition->index ?? null,    //last stop cord index
                        $group->getStartPosition()->latitude,
                        $group->getStartPosition()->longitude,
                        Formatter::altitude()->format($group->getStartPosition()->altitude),
                        $group->getStartPosition()->course,
                        Formatter::time()->convert($group->getStartPosition()->time), //first stop cord time
                        $lastStopPosition ? Formatter::time()->convert($lastStopPosition->time) : null, //last stop cord time
                        $group->getStat('duration')->human(),
                        0, //??
                        [], //??
                    ];
                    break;
                case 'drive':
                    $lastStopPosition = empty($lastStopPosition) ? $group->getStartPosition() : $lastStopPosition;
                    $endPosition = $group->getEndPosition();

                    $json['route']['drives'][] = [
                        $group->getStartPosition()->index,                            //first drive cord index
                        $lastStopPosition->index,                                     //last stop cord index
                        $endPosition->index ?? null,                                  //last drive cord index
                        Formatter::time()->convert($group->getStartPosition()->time), //first drive cord time
                        Formatter::time()->convert($lastStopPosition->time),          //last stop cord time
                        Formatter::time()->convert($group->getEndPosition()->time),   //last drive cord time
                        $group->getStat('duration')->human(),
                        $group->getStat('distance')->human(),
                        $group->getStat('speed_max')->human(),
                        $group->getStat('speed_avg')->human(),
                        $group->hasStat('fuel_consumption') ? $group->getStat('fuel_consumption')->human() : 0,
                        0.00, //??
                        0, //??
                    ];
                    break;
            }
        }

        fwrite ( $fp , json_encode( $json ) );
    }

    private function generateKmlFile($data, $fp)
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<kml xmlns="http://www.opengis.net/kml/2.2">';
        $xml .= '<Document>';
        $xml .= '<name>' . $this->device->name . '</name>';
        $xml .= '<Style id="style1"><LineStyle><color>FF0000E6</color><width>4</width></LineStyle></Style>';
        $xml .= '<Placemark>';
        $xml .= '<name><![CDATA[Track from ' . $this->dateFrom . ' to ' . $this->dateTo . '  UTC]]></name>';
        $xml .= '<styleUrl>#style1</styleUrl>';
        $xml .= '<MultiGeometry>';
        $xml .= '<LineString>';
        $xml .= '<tessellate>1</tessellate>';
        $xml .= '<altitudeMode>clampToGround</altitudeMode>';
        $xml .= '<coordinates>';

        fwrite ( $fp , $xml );

        foreach ($data['groups']->all() as $group) {
            $positions = $group->getStat('positions')->value();

            foreach ($positions as $position) {
                $xml = $position->longitude . ',' . $position->latitude . ',' . ($position->altitude ?? 0) . ' ';

                fwrite ( $fp , $xml );
            }
        }

        $xml = '</coordinates>';
        $xml .= '</LineString>';
        $xml .= '</MultiGeometry>';
        $xml .= '</Placemark>';
        $xml .= '</Document>';
        $xml .= '</kml>';

        fwrite ( $fp , $xml );
    }

    private function generateGpxFile($data, $fp)
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<gpx creator="GPS Software" version="1.0" xmlns="http://www.topografix.com/GPX/1/0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd">';
        $xml .= '<trk>';
        $xml .= '<name>Track from ' . $this->dateFrom . ' to ' . $this->dateTo . '  UTC</name>';
        $xml .= '<type>GPS Tracklog</type>';
        $xml .= '<trkseg>';

        fwrite ( $fp , $xml );

        foreach ($data['groups']->all() as $group) {
            $positions = $group->getStat('positions')->value();

            foreach ($positions as $position) {
                $xml  = '<trkpt lat="' . $position->latitude . '" lon="' . $position->longitude . '">';
                $xml .= '<speed>' . Formatter::speed()->format($position->speed) . '</speed>';
                $xml .= '<ele>' . $position->index . '</ele>';
                $xml .= '<time>' . Formatter::time()->format($position->time) . '</time>';
                $xml .= '</trkpt>';

                fwrite ( $fp , $xml );
            }
        }

        $xml  = '</trkseg>';
        $xml .= '</trk>';
        $xml .= '</gpx>';

        fwrite ( $fp , $xml );
    }

    private function generateCsvFile($data, $fp)
    {
        $params = json_decode($this->device['parameters'], true);
        $params = $params ? array_combine($params, $params) : [];

        //dt,lat,lng,altitude,angle,speed,params
        $fields = array_merge([
            'dt' => 'time',
            'lat' => 'latitude',
            'lng' => 'longitude',
            'altitude' => 'altitude',
            'course' => 'course',
            'speed' => 'speed',
            //'params' => 'other_arr'
        ], $params);

        //heading
        fputcsv($fp, array_keys( $fields ));

        $fields_data = array_values($fields);

        foreach ($data['groups']->all() as $group) {
            $positions = $group->getStat('positions')->value();

            foreach ($positions as $position) {
                $position->time = Formatter::time()->convert($position->time);
                $position->speed = Formatter::speed()->format($position->speed);
                $position->altitude = Formatter::altitude()->format($position->altitude);

                $others = [];
                if (!empty($position->other) && $other_arr = parseXML($position->other)) {
                    foreach ($other_arr as $other_val) {
                        list($key, $val) = explode(':', $other_val);
                        $others[trim($key)] = trim($val);
                    }
                }

                $data = [];

                foreach ($fields_data as $field) {
                    if (property_exists($position, $field)) {
                        $data[$field] = $position->{$field};
                    } else {
                        $data[$field] = isset($others[$field]) ? $others[$field] : null;
                    }
                }

                fputcsv( $fp, $data );
            }
        }
    }
}