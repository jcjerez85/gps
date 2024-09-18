<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\Config\ConfigRepositoryInterface as Config;
use Tobuli\Validation\AdminDatabaseClearFormValidator;

class DatabaseClearController extends BaseController {
    /**
     * @var Config
     */
    private $config;
    /**
     * @var AdminDatabaseClearFormValidator
     */
    private $adminBackupsFormValidator;

    function __construct(Config $config, AdminDatabaseClearFormValidator $adminDatabaseClearFormValidator) {
        parent::__construct();
        $this->config = $config;
        $this->adminDatabaseClearFormValidator = $adminDatabaseClearFormValidator;
    }

    public function panel() {
        $settings = settings('db_clear');

        return View::make('admin::DatabaseClear.panel')->with(compact('settings'));
    }

    public function save() {
        $input = Request::all();

        try
        {
            $this->adminDatabaseClearFormValidator->validate('update', $input);

            $data = [
                'status' => ! empty($input['status']),
                'days'   => $input['days'],
                'from'   => $input['from']
            ];

            settings('db_clear', $data);

            return Redirect::route('admin.tools.index')->withSuccess(trans('front.successfully_saved'));
        }
        catch (ValidationException $e)
        {
            return Redirect::route('admin.tools.index')->withInput()->withErrors($e->getErrors());
        }
    }

    public function getDbSize() {

        $size = getDatabaseSize(['gpswox_traccar','gpswox_web']);

        return formatBytes( $size );
    }
}
