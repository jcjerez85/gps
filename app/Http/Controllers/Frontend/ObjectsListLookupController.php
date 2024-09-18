<?php namespace App\Http\Controllers\Frontend;

use Formatter;
use App\Exceptions\PermissionException;
use App\Http\Controllers\Controller;
use CustomFacades\Repositories\UserRepo;
use CustomFacades\Validators\ObjectsListSettingsFormValidator;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Lookups\Tables\ObjectListLookupTable;


class ObjectsListLookupController extends Controller {

    /*
     * @var Tobuli\Lookups\LookupTable
     */
    protected $lookup;

    public function __construct(ObjectListLookupTable $lookup)
    {
        parent::__construct();

        $this->middleware(function ($request, $next) use ($lookup){
            $this->lookup = $lookup;
            $this->lookup->setUser($this->user);

            if ( ! $this->lookup->checkPermission()) {
                throw new PermissionException();
            }

            return $next($request);
        });
    }

    public function index()
    {
        $data = [
            'html'     => $this->lookup->html(),
            'lookup'   => $this->lookup,
        ];

        if (request()->ajax())
            return view('front::Lookup.modal', $data);
        else
            return view('front::Lookup.index', $data);
    }

    public function table()
    {
        $data = [
            'html'     => $this->lookup->html(),
            'lookup'   => $this->lookup,
        ];

        return view('front::Lookup.table', $data);
    }

    public function data()
    {
        return $this->lookup->render($this->lookup->getPrintView());
    }

    public function edit()
    {
        $this->checkException('users', 'edit', $this->user);

        $numeric_sensors = config('tobuli.numeric_sensors');

        $settings = UserRepo::getListViewSettings($this->user->id);

        $fields = config('tobuli.listview_fields');

        listviewTrans($this->user->id, $settings, $fields);

        return view('front::ObjectsList.edit')->with(compact('fields','settings','numeric_sensors'));
    }

    public function update()
    {
        $this->checkException('users', 'update', $this->user);

        ObjectsListSettingsFormValidator::validate('update', $this->data);

        UserRepo::setListViewSettings($this->user->id, request()->all(['columns','groupby']));

        return ['status' => 1];
    }
}
