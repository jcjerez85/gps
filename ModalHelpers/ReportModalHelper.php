<?php namespace ModalHelpers;

set_time_limit(18000);

use Carbon\Carbon;
use CustomFacades\Repositories\ReportRepo;
use CustomFacades\Server;
use CustomFacades\Validators\ReportFormValidator;
use CustomFacades\Validators\ReportSaveFormValidator;
use Formatter;
use Illuminate\Support\Arr;
use Tobuli\Entities\Geofence;
use Tobuli\Reports\ReportManager;

class ReportModalHelper extends ModalHelper
{
    private $reportManager;

    function __construct()
    {
        parent::__construct();

        $this->reportManager = (new ReportManager())->setUser($this->user);

        Server::setMemoryLimit(config('server.report_memory_limit'));
    }

    public function get()
    {
        $this->checkException('reports', 'view');

        $reports = ReportRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $types = $this->reportManager->getEnabledNameList();

        if ($this->api) {
            $reports = $reports->toArray();
            $reports['url'] = route('api.get_reports');
            foreach ($reports['data'] as &$item) {
                $item['devices'] = Arr::pluck($item['devices'], 'id');
                $item['geofences'] = Arr::pluck($item['geofences'], 'id');
            }
            $new_arr = [];
            foreach ($types as $id => $title) {
                array_push($new_arr, ['id' => $id, 'title' => $title]);
            }
            $types = $new_arr;
        }

        return compact('reports', 'types');
    }

    public function createData()
    {
        $this->checkException('reports', 'create');

        $devices = $this->user->devices()
            ->when(!$this->user->isAdmin(), function($query) {
                return $query->unexpired();
            })
            ->get();

        if (empty($devices))
            return $this->api ? ['status' => 0, 'errors' => ['id' => trans('front.no_devices')]] : modal(trans('front.no_devices'), 'alert');

        $geofences = Geofence::userAccessible($this->user)->get();

        $formats = ReportManager::getFormats();

        $stops = config('tobuli.stops_seconds');

        $filters = [
            '0' => '',
            '1' => trans('front.today'),
            '2' => trans('front.yesterday'),
            '3' => trans('front.before_2_days'),
            '4' => trans('front.before_3_days'),
            '5' => trans('front.this_week'),
            '6' => trans('front.last_week'),
            '7' => trans('front.this_month'),
            '8' => trans('front.last_month'),
        ];

        $metas = $this->reportManager->getMetaList($this->user);

        $types = $types_list = $this->reportManager->getEnabledNameList();

        if ($this->api) {
            $formats = apiArray($formats);
            $stops = apiArray($stops);
            $filters = apiArray($filters);
            $types = apiArray($types);
            $metas = apiArray($metas);
        }

        $reports = ReportRepo::searchAndPaginate(['filter' => ['user_id' => $this->user->id]], 'id', 'desc', 10);
        $reports->setPath(route('reports.index'));

        if ($this->api) {
            $reports = $reports->toArray();
            $reports['url'] = route('api.get_reports');
            $geofences = $geofences->toArray();

            //devices list return as array, not object
            $devices = array_values( $devices->all() );
        } else {
            $devices = groupDevices($devices, $this->user);
        }

        return compact('devices', 'geofences', 'formats', 'stops', 'filters', 'types', 'types_list', 'reports', 'metas');
    }

    public function create()
    {
        if (empty($this->data['id']))
            $this->checkException('reports', 'store');
        else
            $this->checkException('reports', 'update', ReportRepo::find($this->data['id']));

        if ($this->api) {
            if (isset($this->data['devices']) && !is_array($this->data['devices']))
                $this->data['devices'] = json_decode($this->data['devices'], TRUE);

            if (isset($this->data['geofences']) && !is_array($this->data['geofences']))
                $this->data['geofences'] = json_decode($this->data['geofences'], TRUE);
        }

        $this->validate($this->data);

        ReportSaveFormValidator::validate('create', $this->data);

        $now = Carbon::parse( Formatter::time()->convert(date('Y-m-d H:i:s'), 'Y-m-d') );
        $days = $now->diffInDays( Carbon::parse( $this->data['date_from'] ) , false);
        $this->data['from_format'] = $days . ' days ' . (empty($this->data['from_time']) ? '00:00' : $this->data['from_time']);
        $days = $now->diffInDays( Carbon::parse( $this->data['date_to'] ) , false);
        $this->data['to_format'] = $days . ' days ' . (empty($this->data['to_time']) ? '00:00' : $this->data['to_time']);

        if ( ! $this->api ) {
            $this->data['date_from'] .= ' ' . (empty($this->data['from_time']) ? '00:00' : $this->data['from_time']);
            $this->data['date_to']   .= ' ' . (empty($this->data['to_time']) ? '00:00' : $this->data['to_time']);
        }

        $this->data['email'] = implode(';', $this->data['send_to_email']);

        $daily_time = '00:00';
        if (isset($this->data['daily_time']) && preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $this->data['daily_time']))
            $daily_time = $this->data['daily_time'];

        $this->data['daily_time'] = $daily_time;

        $weekly_time = '00:00';
        if (isset($this->data['weekly_time']) && preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $this->data['weekly_time']))
            $weekly_time = $this->data['weekly_time'];

        $this->data['weekly_time'] = $weekly_time;

        $monthly_time = '00:00';
        if (isset($this->data['monthly_time']) && preg_match("/(2[0-4]|[01][1-9]|10):([0-5][0-9])/", $this->data['monthly_time']))
            $monthly_time = $this->data['monthly_time'];

        $this->data['monthly_time'] = $monthly_time;

        if ( !empty($this->data['id']) && empty(ReportRepo::find($this->data['id'])) ) {
            unset($this->data['id']);
        }

        if (empty($this->data['id'])) {

            $item = ReportRepo::create($this->data + [
                    'user_id'           => $this->user->id,
                    'daily_email_sent'  => date('Y-m-d', strtotime("-1 day")),
                    'weekly_email_sent' => date("Y-m-d", strtotime("{$this->user->week_start_weekday} this week")),
                    'monthly_email_sent' => date("Y-m-d", strtotime("first day this month"))
                ]);
        } else {
            $item = ReportRepo::findWhere(['id' => $this->data['id'], 'user_id' => $this->user->id]);
            if (!empty($item))
                ReportRepo::update($item->id, $this->data);
        }

        if (!empty($item)) {
            if (isset($this->data['devices']) && is_array($this->data['devices']) && !empty($this->data['devices']))
                $item->devices()->sync($this->data['devices']);

            if (isset($this->data['geofences']) && is_array($this->data['geofences']) && !empty($this->data['geofences']))
                $item->geofences()->sync($this->data['geofences']);

            if (isset($this->data['pois']) && is_array($this->data['pois']) && !empty($this->data['pois']))
                $item->pois()->sync($this->data['pois']);
        }

        return ['status' => $this->api ? 1 : 2];
    }

    public function generate($data = NULL)
    {
        $this->checkException('reports', 'view');

        if (is_null($data))
            $data = $this->data;

        ReportFormValidator::validate('create', $this->data);

        $data['date_from'] .= ( empty($data['from_time']) ? '' : ' ' . $data['from_time']);
        $data['date_to']   .= ( empty($data['to_time']) ? '' : ' ' . $data['to_time']);

        $this->validate($data);

        if (!isset($data['generate'])) {
            unset($data['_token']);
            unset($data['from_time']);
            unset($data['to_time']);

            return [
                'status' => 3,
                'url' => route($this->api ? 'api.generate_report' : 'reports.update').'?'.http_build_query($data + ['generate' => 1], '', '&')
            ];
        }

        $report = $this->reportManager->fromRequest($data);

        if (!Arr::get($data, 'view'))
            return $report->download();

        return in_array(Arr::get($data, 'format'), ['html', 'json', 'csv']) ? $report->view() : $report->download();
    }

    public function doDestroy($id)
    {
        $item = ReportRepo::find($id);

        $this->checkException('reports', 'remove', $item);

        return compact('item');
    }

    public function destroy()
    {
        $id = array_key_exists('report_id', $this->data) ? $this->data['report_id'] : $this->data['id'];

        $item = ReportRepo::find($id);

        $this->checkException('reports', 'remove', $item);

        ReportRepo::delete($id);

        return ['status' => 1];
    }

    public function validate( & $data)
    {
        # Regenerate string
        $data['send_to_email'] = semicol_explode($data['send_to_email'] ?? '');

        $type = $this->reportManager->getType($data['type']);

        $type->validateInput($data);
    }
}