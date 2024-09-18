<?php

namespace Tobuli\Reports;

use Carbon\Carbon;
use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Entities\CustomField;
use Tobuli\Entities\Device;
use Tobuli\Entities\Geofence;
use Tobuli\Entities\User;
use Tobuli\Reports\Reports\DiemRateDevicesReport;

class ReportManager
{
    public static $types = [
        1  => Reports\GeneralInformationReport::class,
        81 => Reports\GeneralInformationShiftReport::class,
        2  => Reports\GeneralInformationMergedReport::class,
        16 => Reports\GeneralInformationMergedCustomReport::class,
        42 => Reports\GeneralInformationMergedCustom2Report::class,
        49 => Reports\GeneralInformationMergedCustom3Report::class,
        56 => Reports\GeneralInformationMergedCustom4Report::class,
        66 => Reports\GeneralInformationMergedCustom5Report::class,
        69 => Reports\GeneralInformationMergedCustom6Report::class,
        3  => Reports\DrivesStopsReport::class,
        18 => Reports\DrivesStopsGeofencesReport::class,
        19 => Reports\DrivesStopsDriversReport::class,
        21 => Reports\DrivesStopsDriversBusinessReport::class,
        22 => Reports\DrivesStopsDriversPrivateReport::class,
        41 => Reports\DrivesStopsReportSimplified::class,
        72 => Reports\DriveTimeReport::class,
        40 => Reports\StopsReport::class,
        4  => Reports\TravelSheetReport::class,
        39 => Reports\TravelSheetReportCustom::class,
        61 => Reports\TravelSheetBusinessPrivateReport::class,
        5  => Reports\OverspeedsReport::class,
        59 => Reports\OverspeedsRoadsReport::class,
        47 => Reports\OverspeedsInGeofenceReport::class,
        33 => Reports\OverspeedCustomReport::class,
        34 => Reports\OverspeedCustomSummaryReport::class,
        52 => Reports\OverspeedsSpeedECMReport::class,
        51 => Reports\SpeedCompareGpsEcmReport::class,
        45 => Reports\SpeedReport::class,
        6  => Reports\UnderspeedsReport::class,
        53 => Reports\GeofencesReport::class,
        7  => Reports\GeofencesInOutReport::class,
        71 => Reports\GeofencesInOutMergedReport::class,
        15 => Reports\GeofencesInOut24ModeReport::class,
        20 => Reports\GeofencesInOutEngineReport::class,
        28 => Reports\GeofencesShiftReport::class,
        31 => Reports\GeofencesTouchAllReport::class,
        44 => Reports\GeofencesTouchAllReport2::class,
        57 => Reports\GeofencesInGroupReport::class,
        80 => Reports\DevicesInGeofencesReport::class,
        8  => Reports\EventDeviceReport::class,
        10 => Reports\FuelLevelReport::class,
        11 => Reports\FuelFillingsReport::class,
        12 => Reports\FuelTheftsReport::class,
        13 => Reports\TemperatureReport::class,
        77 => Reports\TemperatureWithStatsReport::class,
        14 => Reports\RagReport::class,
        63 => Reports\RagWithTurnReport::class,
        23 => Reports\RagSeatbeltReport::class,
        70 => Reports\RagGeofencesReport::class,
        25 => Reports\ObjectHistoryReport::class,
        62 => Reports\OdometerReport::class,
        78 => Reports\OdometerDailyReport::class,
        58 => Reports\EngineHoursCurrentReport::class,
        29 => Reports\EngineHoursVirtualReport::class,
        64 => Reports\EngineHoursGraphReport::class,
        48 => Reports\WorkHoursDailyReport::class,
        30 => Reports\IgnitionOnOff24ModeReport::class,
        32 => Reports\SentCommandsReport::class,
        35 => Reports\InstallationDeviceAllReport::class,
        36 => Reports\InstallationDeviceOfflineReport::class,
        38 => Reports\OfflineDeviceReport::class,
        37 => Reports\LoadReport::class,
        67 => Reports\GeofencesStopReport::class,
        68 => Reports\GeofencesStopShiftReport::class,
        43 => Reports\RoutesReport::class,
        65 => Reports\RoutesSummarizedReport::class,
        24 => Reports\BirlaCustomReport::class,
        27 => Reports\AutomonCustomReport::class,
        46 => Reports\DeviceExpensesReport::class,
        50 => Reports\ChecklistReport::class,
        54 => Reports\PoiStopDurationReport::class,
        55 => Reports\PoiIdleDurationReport::class,
        60 => Reports\CartDailyCleaningReport::class,
        73 => Reports\DiemRateDevicesReport::class,
        74 => Reports\DiemRateDriversReport::class,
        75 => Reports\FuelTankUsageReport::class,
        76 => Reports\FuelTankUsageDriverReport::class,
        79 => Reports\DriverDailyDistanceReport::class,
    ];

    /**
     * @var null|User
     */
    private $user = null;

    public static function getMetaList(User $user)
    {
        $list = [
            'device.group_id' => trans('validation.attributes.group_id'),
            'device.sim_number' => trans('validation.attributes.sim_number'),
            'device.imei' => trans('validation.attributes.imei'),
            //'device.protocol' => trans('validation.attributes.protocol'),
            'device.device_model' => trans('front.model'),
            'device.object_owner' => trans('validation.attributes.object_owner'),
            'device.plate_number' => trans('validation.attributes.plate_number'),
            'device.registration_number' => trans('validation.attributes.registration_number'),
            'device.expiration_date' => trans('validation.attributes.expiration_date'),
            'device.vin' => trans('validation.attributes.vin'),
            'history.drivers' => trans('front.drivers'),
        ];

        $list = array_filter($list, function($stat, $key) use ($user){
            list($model,$attribute) = explode('.', $key);

            if ($model === 'device')
                return $user->can('view', new Device(), $attribute);

            return true;
        }, ARRAY_FILTER_USE_BOTH);

        foreach (CustomField::filterByModel('device')->get() as $customField) {
            $list["device.custom_fields.{$customField->id}"] = $customField->title;
        }

        return $list;
    }

    public static function getFormats(): array
    {
        return [
            'html' => trans('front.html'),
            'json' => trans('front.json'),
            'xls' => trans('front.xls'),
            'xlsx' => trans('front.xlsx'),
            'pdf' => trans('front.pdf'),
            'pdf_land' => trans('front.pdf_land'),
            //'csv' => trans('front.csv'),
        ];
    }

    public static function resolveTypeReport($type)
    {
        $class = self::$types[$type] ?? null;

        if (!$class) {
            return null;
        }

        return new $class();
    }

    public static function getTitle($type)
    {
        $report = self::resolveTypeReport($type);

        return $report ? $report->title() : null;
    }

    /**
     * @return Report[]
     */
    public function getEnabledList(): array
    {
        $list = [];

        foreach (self::$types as $typeId => $class) {
            if (!$class::isEnabled()) {
                continue;
            }

            $report = new $class();
            $report->setUser($this->user);
            $list[$typeId] = $report;
        }

        return $list;
    }

    public function getEnabledArrayList(): array
    {
        $list = $this->getEnabledList();

        array_walk($list, function (Report &$report) {
            $report = $report->toArray();
        });

        return $list;
    }

    public function getEnabledNameList(): array
    {
        $list = $this->getEnabledList();

        array_walk($list, function (Report &$report) {
            $report = $report->title();
        });

        return $list;
    }

    public function getType(int $id): Report
    {
        $report = self::resolveTypeReport($id);

        if (!$report) {
            throw new \Exception('Not found');
        }

        $report->setUser($this->user);

        return $report;
    }

    /**
     * @param $type
     * @return DeviceReport
     */
    public function report($type)
    {
        $class = self::$types[$type];

        return new $class();
    }

    public function fromRequest($data)
    {
        //check report
        $report = $this->report($data['type']);

        $user = auth()->user();
        Formatter::byUser($user);

        $data['date_from'] = Formatter::time()->reverse($data['date_from']);
        $data['date_to'] = Formatter::time()->reverse($data['date_to']);
        $data['user'] = $user;

        if ( ! empty($data['devices']))
            $data['devices'] = $user->devices()
                ->with('sensors')
                ->when(!$user->isAdmin(), function($query) {
                    return $query->unexpired();
                })
                ->whereIn('id', $data['devices']);

        if ( ! empty($data['geofences']))
            $data['geofences'] = Geofence::userAccessible($user)->whereIn('id', $data['geofences'])->get();

        if ( ! empty($data['pois']))
            $data['pois'] = $user->pois()->whereIn('id', $data['pois'])->get();

        return $this->from($data);
    }

    /**
     * @param \Tobuli\Entities\Report $report
     * @param $data
     * @return DeviceReport
     */
    public function fromEntity(\Tobuli\Entities\Report $report, $data)
    {
        //check report
        $this->report($data['type']);

        Formatter::byUser($data['user']);

        $data = array_merge($data, $report->toArray());

        $data['date_from'] = Formatter::time()->reverse($data['date_from']);
        $data['date_to'] = Formatter::time()->reverse($data['date_to']);

        if ($report->devices()->count()) {
            $deviceQuery = $report->devices();
        } else {
            $deviceQuery = $data['user']->devices();
        }
        $data['devices'] = $deviceQuery
            ->with('sensors')
            ->when(!$data['user']->isAdmin(), function($query) {
                return $query->unexpired();
            });

        $data['geofences'] = $report->geofences;
        $data['pois'] = $report->pois;

        return $this->from($data);
    }

    public function from($data)
    {
        $report = $this->report($data['type']);
        $report->setUser($data['user']);
        $report->setFormat($data['format']);
        $report->setRange($data['date_from'], $data['date_to']);

        if ( ! empty($data['metas']))
            $report->setMetas($data['metas']);
        if ( ! empty($data['devices']))
            $report->setDevicesQuery($data['devices']);
        if ( ! empty($data['geofences']))
            $report->setGeofences($data['geofences']);
        if ( ! empty($data['pois']))
            $report->setPois($data['pois']);
        if ( ! empty($data['parameters']))
            $report->setParameters($data['parameters']);
        if ( ! empty($data['speed_limit']))
            $report->setSpeedLimit(Formatter::speed()->reverse($data['speed_limit']));
        if ( ! empty($data['stops']))
            $report->setStopSeconds($data['stops']);
        if ( ! empty($data['show_addresses']))
            $report->setShowAddresses(true);
        if ( ! empty($data['zones_instead']))
            $report->setZonesInstead(true);
        if ( ! empty($data['skip_blank_results']))
            $report->setSkipBlankResults(true);

        return $report;
    }

    public function debug($data = [])
    {
        $data = array_merge([
            'user' => auth()->user(),
            'format' => 'html',
            'date_from' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
            'date_to'   => Carbon::now()->format('Y-m-d H:i:s'),
            'geofences' => auth()->user()->geofences,
            'devices'   => \Tobuli\Entities\Device::with('sensors')->where('name', 'like', '%Demo%')->limit(5)->get(),
            'speed_limit' => 60,
            //'zones_instead' => true,
        ], $data);

        foreach ($this::$types as $type => $class) {
            $data['type'] = $type;

            try {
                $report = $this->from($data);
                echo $report->view();
            } catch (\Exception $e) {
                var_dump(Arr::except($data, ['user', 'devices', 'geofences']));
                throw $e;
            }
        }
    }

    /**
     * @param null|User $user
     * @return $this
     */
    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}