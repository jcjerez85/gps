<?php namespace App\Http\Controllers\Admin;


use App\Exceptions\ResourseNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\DeviceExpensesType;
use Tobuli\Exceptions\ValidationException;

class DeviceExpensesTypesController extends BaseController
{
    public function index()
    {
        return view('admin::DeviceExpensesTypes.' . (Request::ajax() ? 'table' : 'index'), [
            'types' => DeviceExpensesType::paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin::DeviceExpensesTypes.create');
    }

    public function store()
    {
        $this->validateInput();

        DeviceExpensesType::create(['name' => request('name')]);

        return ['status' => 1];
    }

    public function edit($id)
    {
        $type = DeviceExpensesType::find($id);

        if (is_null($type))
            throw new ResourseNotFoundException(trans('validation.attributes.type'));

        return view('admin::DeviceExpensesTypes.edit', [
            'type' => $type
        ]);
    }

    public function update($id)
    {
        $this->validateInput();

        $type = DeviceExpensesType::find($id);

        if (is_null($type))
            throw new ResourseNotFoundException(trans('validation.attributes.type'));

        $type->update(['name' => request('name')]);

        return ['status' => 1];
    }

    public function destroy()
    {
        DeviceExpensesType::destroy(request('id'));

        return ['status' => 1];
    }

    private function validateInput()
    {
        if ( ! request()->filled('name'))
            throw new ValidationException([
                'name' => str_replace(':attribute', trans('validation.attributes.name'), trans('validation.required')),
            ]);
    }
}
