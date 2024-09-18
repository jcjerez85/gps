<?php namespace ModalHelpers;

use App\Exceptions\PermissionException;
use App\Exceptions\ResourseNotFoundException;
use Auth;
use CustomFacades\Repositories\ReportLogRepo;
use CustomFacades\Repositories\UserRepo;
use Tobuli\Entities\ReportLog;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Reports\ReportManager;

ini_set('memory_limit', '-1');
set_time_limit(600);

class ReportLogModalHelper extends ModalHelper
{
	private $mimes = [];

	function __construct()
	{
		parent::__construct();

		$this->mimes = [
			'html' => 'plain/text',
			'xls' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'pdf' => 'application/pdf',
            'pdf_land' => 'application/pdf',
            'csv' => 'text/csv',
            'json' => 'application/json',
		];

        $this->exts = [
            'html' => 'html',
            'xls' => 'xls',
            'xlsx' => 'xlsx',
            'pdf' => 'pdf',
            'pdf_land' => 'pdf',
            'csv' => 'csv',
            'json' => 'json',
        ];
	}

	public function get($limit = 10)
	{
        $logs = ReportLog::userAccessible($this->user)
            ->search($this->data['search_phrase'] ?? null)
            ->filter($this->data)
            ->toPaginator($limit, 'id', 'desc');

		foreach ( $logs as $index => $log ) {
			$logs[ $index ]->type_text   = ReportManager::getTitle($log->type);
			$logs[ $index ]->format_text = ReportManager::getFormats()[$log->format] ?? $log->format;
		}

		return $logs;
	}

	public function download($id)
	{
        $log = ReportLog::userAccessible($this->user)->find($id);

		if ( $log ) {
			$data = $log->data;

			$headers = [
				'Content-Type' => $this->mimes[ $log->format ],
				'Content-Length' => $log->size,
				'Content-Disposition' => 'attachment; filename="' . $log->title . '.' . $this->exts[ $log->format ] . '"'
			];
		}

		return compact('data', 'headers');
	}

	public function destroy()
	{
		if ( empty($this->data['id']) )
		    throw new ResourseNotFoundException('front.report');

		$ids = is_array( $this->data['id'] ) ? $this->data['id'] : [ $this->data['id'] ];

        $items = ReportLog::userAccessible($this->user)->whereIn('id', $ids)->cursor();

        foreach ($items as $item) {
            $item->delete();
        }

		return ['status' => 1];
	}

    public function destroyAll(): array
    {
        $items = ReportLog::userAccessible($this->user)->cursor();

        foreach ($items as $item) {
            $item->delete();
        }

        return ['status' => 1];
    }
}