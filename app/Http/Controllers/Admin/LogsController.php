<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\File\TrackerLog;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Helpers\Tracker;
use Tobuli\Helpers\TrackerConfig;

class LogsController extends BaseController {
    private $section = 'logs';

    private $trackerConfig;

    public function __construct(TrackerConfig $trackerConfig)
    {
        parent::__construct();

        $this->trackerConfig = $trackerConfig;

        $this->levels = [
            //'ALL' => 'ALL',
            //'FINEST' => 'FINEST',
            //'FINER' => 'FINER',
            //'FINE' => 'FINE',
            //'CONFIG' => 'CONFIG',
            'info'    => 'INFO',
            'warning' => 'WARNING',
            'severe'  => 'SEVERE',
            'off'     => 'OFF',
        ];
    }

    public function index()
    {
        $items = TrackerLog::all()
            ->filter(function ($item) {
                return strpos($item->basename, 'wrapper.log') === false;
            })
            ->sortByDesc(function($item) {
                return $item->created_at;
            })
            ->paginate(20);

        return view('admin::Logs.' . (Request::ajax() ? 'table' : 'index'), [
            'items'   => $items,
            'section' => $this->section,
        ]);
    }

    /**
     * Download tracker log file.
     *
     * @return mixed
     */
    public function download($id)
    {
        try {
            $file = TrackerLog::find($id);
        } catch (\Exception $e) {
            return redirect(route('admin.logs.index'));
        }

        return Response::download($file->path);
    }

    /**
     * Delete tracker log files.
     *
     * @return mixed
     */
    public function delete()
    {
        $ids = Request::input('id');

        if ( ! is_array($ids))
            $ids = [$ids];

        $ids = array_filter($ids);

        foreach ($ids as $id) {
            $file = TrackerLog::find($id);
            $file->delete();
            
            try {
                TrackerLog::find('wrapper.log.' . date('Ymd', strtotime($file->created_at)) . '.gz')->delete();
            } catch (\Exception $e) {}
        }

        return Response::json(['status' => 1]);
    }

    public function configForm()
    {
        $level = $this->trackerConfig->get('logger.level');

        return view('admin::Logs.config', [
            'current' => strtolower($level),
            'levels'  => $this->levels,
            'section' => $this->section,
        ]);
    }

    public function configSet()
    {
        $validator = Validator::make(request()->all(), [
            'level' => 'required|in:' . implode(',', array_keys($this->levels)),
        ]);

        if ($validator->fails())
            throw new ValidationException($validator->errors());

        $this->trackerConfig->set('logger.level', request()->get('level'));
        $this->trackerConfig->generate();

        (new Tracker())->actor($this->user)->restart();

        return Response::json(['status' => 1]);
    }
}
