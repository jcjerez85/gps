<?php namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Repositories\MapIcon\MapIconRepositoryInterface as MapIcon;
use Tobuli\Repositories\Poi\PoiRepositoryInterface as Poi;
use Tobuli\Validation\MapIconUploadValidator;

class MapIconsController extends BaseController{
    private $section = 'map_icons';

    private $mapIcon;
    /**
     * @var Poi
     */
    private $poi;
    /**
     * @var MapIconUploadValidator
     */
    private $mapIconUploadValidator;

    function __construct(MapIcon $mapIcon, Poi $poi, MapIconUploadValidator $mapIconUploadValidator)
    {
        parent::__construct();
        $this->mapIcon = $mapIcon;
        $this->poi = $poi;
        $this->mapIconUploadValidator = $mapIconUploadValidator;
    }

    public function index() {
        $input = Request::all();

        $items = $this->mapIcon->searchAndPaginate($input, 'path', 'desc', 40);
        $section = $this->section;

        return View::make('admin::'.ucfirst($this->section).'.' . (Request::ajax() ? 'table' : 'index'))
            ->with(compact('items', 'input', 'section'));
    }

    public function store() {
        $file = Request::file('file');
        try
        {
            $this->mapIconUploadValidator->validate('create', ['file' => $file]);
            $file = Request::file('file');
            list($w, $h) = getimagesize($file);
            $destinationPath = 'images/map_icons';
            $filename = uniqid('', TRUE).'.'.$file->getClientOriginalExtension();
            $file->move($destinationPath, $filename);
            $this->mapIcon->create(['path' => $destinationPath.'/'.$filename, 'width' => $w, 'height' => $h]);


            /*$base_public_path = base_path('../../').'/public/frontend/images/map_icons';
            File::cleanDirectory($base_public_path);
            File::copyDirectory(base_path('public').'/'.$destinationPath, $base_public_path);*/
            return Response::json(['status' => 1]);
        }
        catch (ValidationException $e)
        {
            return Response::make($e->getErrors()->first(), '406');
        }
    }

    public function destroy() {
        $ids = Request::input('id');
        if (is_array($ids) && $nr = count($ids)) {
            $all = $this->mapIcon->count();
            if ($nr >= $all) {
                return Response::json(['status' => 0, 'error' => trans('admin.cant_delete_all')]);
            }
            $icon = $this->mapIcon->whereNotInFirst($ids);

            $this->poi->updateWhereIconIds($ids, ['map_icon_id' => $icon->id]);
            foreach($ids as $id) {
                $del_icon = $this->mapIcon->find($id);
                if ($del_icon) {
                    $filename = public_path().'/'.$del_icon->path;
                    if (File::exists($filename)) {
                        File::delete($filename);
                    }
                    $this->mapIcon->delete($id);
                }
            }

            /*File::cleanDirectory(base_path('../../').'/public/frontend/images/map_icons');
            File::copyDirectory('frontend/images/map_icons', base_path('../../').'/public/frontend/images/map_icons');*/
        }

        return Response::json(['status' => 1]);
    }
}
