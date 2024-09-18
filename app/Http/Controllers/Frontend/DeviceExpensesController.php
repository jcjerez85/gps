<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Repositories\DeviceRepo;
use CustomFacades\Validators\DeviceExpensesFormValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Tobuli\Entities\Device;
use Tobuli\Entities\DeviceExpense;
use App\Http\Controllers\Controller;
use Tobuli\Entities\DeviceExpensesType;

class DeviceExpensesController extends Controller
{

    public function index($device_id = null)
    {
        $query = $this->getExpensesQuery($device_id);

        return view('front::DeviceExpenses.index', [
            'total'     => $device_id ? $query->sum(DB::raw('`quantity`*`unit_cost`')) : null,
            'expenses'  => $query->paginate(15),
            'device_id' => $device_id,
        ]);
    }

    public function modal($device_id = null)
    {
        $query = $this->getExpensesQuery($device_id);

        return view('front::DeviceExpenses.modal', [
            'total'     => $device_id ? $query->sum(DB::raw('`quantity`*`unit_cost`')) : null,
            'expenses'  => $query->paginate(15),
            'device_id' => $device_id,
        ]);
    }

    public function table($device_id = null)
    {
        $view  = $device_id ? 'front::DeviceExpenses.table' : 'front::DeviceExpenses.table_list';
        $query = $this->getExpensesQuery($device_id);

        return view($view, [
            'total'     => $device_id ? $query->sum(DB::raw('`quantity`*`unit_cost`')) : null,
            'expenses'  => $query->paginate(15),
            'device_id' => $device_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->checkException('device_expenses', 'store');

        $expense_types = DeviceExpensesType::all()->pluck('name', 'id');

        if ( ! request()->filled('device_id'))
            return view('front::DeviceExpenses.create', [
                'devices'        => $this->user->devices->pluck('name', 'id'),
                'expenses_types' => $expense_types,
            ]);

        if (is_null($device = Device::find(request('device_id'))))
            throw new ResourseNotFoundException(trans('global.device'));

        $this->checkException('devices', 'show', $device);

        return view('front::DeviceExpenses.create', [
            'device_id'      => $device->id,
            'expenses_types' => $expense_types,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->checkException('device_expenses', 'store');

        DeviceExpensesFormValidator::validate('create', $this->data);

        $device = Device::find($request->device_id);

        if (is_null($device))
            throw new ResourseNotFoundException(trans('global.device'));

        $this->checkException('devices', 'show', $device);

        $device->expenses()->create($request->all() + ['user_id' => $this->user->id]);

        return response()->json(['status' => 1]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($expense = DeviceExpense::find($id)))
            throw new ResourseNotFoundException(trans('front.expenses'));

        $this->checkException('devices', 'show', $expense->device);
        $this->checkException('device_expenses', 'edit', $expense);

        return view('front::DeviceExpenses.edit', [
            'device_id'      => request('device_id'),
            'expense'        => $expense,
            'expenses_types' => DeviceExpensesType::all()->pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DeviceExpensesFormValidator::validate('update', $this->data);

        if (is_null($expense = DeviceExpense::find($id)))
            throw new ResourseNotFoundException('front.expenses');

        $this->checkException('devices', 'show', $expense->device);
        $this->checkException('device_expenses', 'edit', $expense);

        $expense->update($request->all());

        return response()->json(['status' => 1]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($expense = DeviceExpense::find($id)))
            throw new ResourseNotFoundException('front.expenses');

        $this->checkException('devices', 'show', $expense->device);
        $this->checkException('device_expenses', 'remove', $expense);

        $expense->delete();

        return response()->json(['status' => 1]);
    }

    public function suppliers()
    {
        $suppliers = DeviceExpense::groupBy('supplier')
            ->select(['supplier', DB::raw('count(*) as total')])
            ->userAccessible($this->user)
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get()->toArray();

        $suggestions = [];

        foreach ($suppliers as $supplier) {
            $suggestions[]['value'] = $supplier['supplier'];
        }

        return Response::json(['suggestions' => $suggestions]);
    }

    private function getExpensesQuery($device_id)
    {
        $this->checkException('device_expenses', 'view');

        if (is_null($device_id)) {
            return DeviceExpense::join('user_device_pivot', 'device_expenses.device_id', '=', 'user_device_pivot.device_id')
                ->where('user_device_pivot.user_id', $this->user->id)
                ->select('device_expenses.*')
                ->orderBy('device_expenses.date');
        }

        $device = Device::find($device_id);

        $this->checkException('devices', 'show', $device);

        return $device->expenses()->orderBy('date');
    }
}
