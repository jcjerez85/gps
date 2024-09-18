<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use CustomFacades\ModalHelpers\ReportLogModalHelper;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Tobuli\ConfirmedAction\Prompt;

class ReportLogsController extends Controller
{
	private $section = 'report_logs';
	
    public function index()
    {
        $data = [];
		$data['logs'] = ReportLogModalHelper::get(25);
        $data['section'] = $this->section;

        return view('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))->with($data);
    }

    public function edit($id)
    {
		$data = ReportLogModalHelper::download($id);
	   
		return Response::make( $data['data'], 200, $data['headers']);
    }

    public function destroy()
    {
        $all = $this->data['all'] ?? false;

        $data = $all ? ReportLogModalHelper::destroyAll() : ReportLogModalHelper::destroy();

        return Request::ajax() ? Response::json($data) : null;
    }
}