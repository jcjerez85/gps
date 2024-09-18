<?php namespace Tobuli\Reports\Reports;


use Tobuli\Entities\DeviceExpense;
use Tobuli\Entities\DeviceExpensesType;
use Tobuli\Reports\DeviceReport;

class DeviceExpensesReport extends DeviceReport
{
    const TYPE_ID = 46;

    protected $enableFields = ['devices', 'metas'];

    public function getInputParameters(): array
    {
        $all = ['all' => 'All'];

        return [
            \Field::select('expense_type', trans('validation.attributes.expense_type'))
                ->setOptions($all)
                ->setOptionsViaQuery(DeviceExpensesType::query()->toBase(), 'name')
                ->setRequired()
            ,
            \Field::select('supplier', trans('validation.attributes.supplier'))
                ->setOptions($all)
                ->setOptionsViaQuery(DeviceExpense::select('supplier')->distinct()->toBase(), 'supplier', 'supplier')
                ->setRequired()
            ,
        ];
    }

    public static function isEnabled()
    {
        return expensesTypesExist();
    }

    public function typeID()
    {
        return self::TYPE_ID;
    }

    public function title()
    {
        return trans('front.expenses');
    }

    protected function generateDevice($device)
    {
        $query = $device->expenses()
            ->whereBetween('date', [$this->date_from, $this->date_to])
            ->with('type');

        if (($type = $this->parameters['expense_type']) != 'all') {
            $query->whereHas('type', function ($q) use ($type) {
                $q->where('id', $type);
            });
        }

        if (($supplier = $this->parameters['supplier']) != 'all') {
            $query->where('supplier', $supplier);
        }

        $expenses = $query->get();

        return [
            'meta' => $this->getDeviceMeta($device),
            'data' => [
                'expenses' => $expenses->toArray(),
                'sum'      => $expenses->sum(function ($expense) { return $expense->total; }),
            ],
        ];
    }

}