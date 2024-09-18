<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Backup;
use Tobuli\Validation\AdminBackupsFormValidator;

class BackupsController extends BaseController {
    /**
     * @var array
     */
    private $periods;
    /**
     * @var array
     */
    private $hours;
    /**
     * @var array
     */
    private $types;
    /**
     * @var AdminBackupsFormValidator
     */
    private $adminBackupsFormValidator;

    function __construct(AdminBackupsFormValidator $adminBackupsFormValidator)
    {
        parent::__construct();

        $this->adminBackupsFormValidator = $adminBackupsFormValidator;

        $this->periods = [
            '1' => '1 '.trans('front.day'),
            '3' => '3 '.trans('front.days'),
            '7' => '7 '.trans('front.days'),
            '30' => '30 '.trans('front.days'),
        ];

        $this->hours = [
            '00:00' => '00:00',
            '01:00' => '01:00',
            '02:00' => '02:00',
            '03:00' => '03:00',
            '04:00' => '04:00',
            '05:00' => '05:00',
            '06:00' => '06:00',
            '07:00' => '07:00',
            '08:00' => '08:00',
            '09:00' => '09:00',
            '10:00' => '10:00',
            '11:00' => '11:00',
            '12:00' => '12:00',
            '13:00' => '13:00',
            '14:00' => '14:00',
            '15:00' => '15:00',
            '16:00' => '16:00',
            '17:00' => '17:00',
            '18:00' => '18:00',
            '19:00' => '19:00',
            '20:00' => '20:00',
            '21:00' => '21:00',
            '22:00' => '22:00',
            '23:00' => '23:00',
        ];

        $this->types = [
            'auto' => trans('front.automatic'),
            'custom' => trans('global.custom'),
        ];
    }

    public function index() {
        return View::make('admin::Backups.index')->with([
            'settings' => settings('backups'),
            'periods' => $this->periods,
            'hours' => $this->hours,
            'types' => $this->types
        ]);
    }

    public function panel() {
        return View::make('admin::Backups.panel')->with([
            'settings' => settings('backups'),
            'periods' => $this->periods,
            'hours' => $this->hours,
            'types' => $this->types
        ]);
    }

    public function save() {
        $input = Request::all();
        $settings = settings('backups');
        try
        {
            if (config('app.server') == 'demo')
                throw new ValidationException(['id' => trans('front.demo_acc')]);

            $this->adminBackupsFormValidator->validate('update', $input);

            beginTransaction();
            try {
                if (!isset($settings['next_backup']) || $settings['period'] != $input['period'] || $settings['hour'] != $input['hour'])
                    $settings['next_backup'] = strtotime(date('Y-m-d', strtotime('+'.$input['period'].' days')).' '.$input['hour']);

                $settings['type'] = $input['type'];
                $settings['ftp_server'] = $input['ftp_server'];
                $settings['ftp_port'] = $input['ftp_port'];
                $settings['ftp_username'] = $input['ftp_username'];
                $settings['ftp_password'] = empty($input['ftp_password']) ? $settings['ftp_password'] : $input['ftp_password'];
                $settings['ftp_path'] = $input['ftp_path'];
                $settings['period'] = $input['period'];
                $settings['hour'] = $input['hour'];

                settings('backups', $settings);
            }
            catch (\Exception $e) {
                rollbackTransaction();
                throw new ValidationException(['id' => trans('global.unexpected_db_error')]);
            }

            commitTransaction();

            try {
                (new Backup($settings))->check();
            }
            catch(\Exception $e) {
                throw new ValidationException(['id' => $e->getMessage()]);
            }

            return Redirect::route('admin.backups.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.backups.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function test()
    {
        $settings = settings('backups');

        if (empty($settings))
            return Response::json(['status' => trans('front.unexpected_error')]);

        try {
            (new Backup($settings))->check();

            $message = trans('front.successfully_uploaded');
            $status = 1;
        }
        catch(\Exception $e) {
            $message = $e->getMessage();
            $status = 0;
        }

        return Response::json(['status' => $status, 'message' => $message]);
    }

    public function logs()
    {
        $settings = settings('backups');

        return View::make('admin::Backups.logs')->with(compact('settings'));
    }
}
