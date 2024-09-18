<?php

namespace Tobuli\Lookups;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tobuli\Entities\User;
use Tobuli\Lookups\Styler\ExcelCellStringifier;
use Yajra\DataTables\Services\DataTable;

abstract class LookupTable extends DataTable
{
    /*
     * @var User
     */
    protected $user;

    /*
     * @var LookupModel
     */
    protected $lookupModel;

    /*
     * View for print/export
     * @var string
     */
    protected $printPreview = 'front::Lookup.print';

    /*
     * Ability store table settings
     * @var boolean
     */
    protected $settingable = true;

    /*
     * Auto refresh timeout
     * @var integer
     */
    protected $autorefresh = 0;

    abstract protected function getLookupClass();
    abstract public function getTitle();
    abstract public function getIcon();
    abstract public function getRowActions($model);
    abstract public function getDefaultColumns();
    abstract public function baseQuery();

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query);
    }

    static public function route($action, $options = []) {
        return app(get_called_class())->getRoute($action, $options);
    }

    public function getTableId()
    {
        return class_basename(get_class($this));
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        if (is_null($this->user))
            return getActingUser();

        return $this->user;
    }

    public function getPrintView()
    {
        return $this->printPreview;
    }

    public function isHtmlBuild()
    {
        if ($action = $this->request()->get('action') AND in_array($action, ['print', 'csv', 'excel']))
            return false;

        return true;
    }

    public function isExport(): bool
    {
        return ($action = $this->request()->get('action')) && in_array($action, ['print', 'csv', 'pdf', 'excel']);
    }

    /**
     * Get lookup model
     *
     * @return \Tobuli\Lookups\LookupModel
     */
    public function lookupModel() {
        if ( ! is_null($this->lookupModel))
            return $this->lookupModel;

        $class = $this->getLookupClass();

        return $this->lookupModel = new $class($this->getUser());
    }

    /*
     * @return boolean
     */
    public function checkPermission()
    {
        return $this->getUser()->can('view', $this->lookupModel()->model());
    }

    public function getRouteLookupName()
    {
        if ( ! is_null($this->route_lookup_name))
            return $this->route_lookup_name;

        $class = class_basename(get_class($this));

        return $this->route_lookup_name = Str::snake( str_replace('LookupTable', '', $class) );
    }

    public function excel()
    {
        $collection = $this->getDataForExport();

        return Excel::download(
            new ExcelCellStringifier($collection),
            $this->getFilename() . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        )->deleteFileAfterSend(true);
    }

    public function getRoutes($options = [])
    {
        $parameters = array_merge([
            'lookup' => $this->getRouteLookupName()
        ], $options);

        return [
            'index'  => route('lookup.index', $parameters),
            'table'  => route('lookup.table', $parameters),
            'data'   => route('lookup.data', $parameters),
            'edit'   => route('lookup.edit', $parameters),
            'update' => route('lookup.update', $parameters),

            'csv'    => route('lookup.data', $parameters + ['action' => 'csv']),
            'excel'  => route('lookup.data', $parameters + ['action' => 'excel']),
            'pdf'    => route('lookup.data', $parameters + ['action' => 'pdf']),
        ];
    }

    public function getRoute($action, $options = [])
    {
        $route = Arr::get($this->getRoutes($options), $action);

        if ( ! $route)
            throw new \Exception("'".get_class($this)."' route action '$action' not set");

        return $route;
    }

    public function isAutoRefresh()
    {
        return $this->autorefresh > 0;
    }

    public function getRefreshMiliseconds()
    {
        return $this->autorefresh * 1000;
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $query = $this->baseQuery();

        $query = $this->extraQuery($query);

        $query = $this->applyScopes($query);

        if ($relations = $this->getRelations())
            $query->with($relations);

        return $query;
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        $query = null;
        if (method_exists($this, 'query')) {
            $query = app()->call([$this, 'query']);
        }


        /** @var \Yajra\DataTables\DataTableAbstract $dataTable */
        $dataTable = app()->call([$this, 'dataTable'], compact('query'));

        //$datatable = $this->datatables->eloquent($model);

        foreach ($this->getColumns() as $column) {
            $dataTable->editColumn($column['data'], function ($model) use ($column) {
                return $this->renderColumn($model, $column);
            });
        }

        if ($this->hasRowActions() && !$this->isExport())
            $dataTable->addColumn('action', function ($device) {
                $actions = $this->getRowActions($device);

                return $this->renderRowActions($actions);
            });

        //for html render in cell
        $dataTable->escapeColumns([]);

        return $dataTable->make(true);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {
        $this->builder()
            ->columns($this->getColumns())
            ->ajax($this->getAjaxParameters())
            ->setTableAttributes([
                'id'    =>  $this->getTableId(),
                'class' => 'table table-list',
                'style' => 'width: 100%'
            ])
            ->parameters($this->getBuilderParameters());

        $actions = $this->getMainActions();

        if ($actions || $this->hasRowActions())
            $this->builder()->addAction([
                    'data'           => 'action',
                    'name'           => 'action',
                    'title'          => $this->renderMainActions($actions),
                    'render'         => null,
                    'orderable'      => false,
                    'searchable'     => false,
                    'exportable'     => false,
                    'printable'      => false,
                    'className'      => 'text-right',
                    'titleAttr'      => '',
                    'attributes'     => [
                        'aria-label' => '',
                    ],
            ]);

        return $this->builder();
    }

    public function getColumns() {
        return $this->getCurrentColumns()->toArray();
    }

    public function getCurrentColumns()
    {
        $columns = $this->getRememberColumns();

        if ( ! $columns)
            $columns = $this->getDefaultColumns();

        return $this->lookupModel()->getColumnsOnly($columns);
    }

    public function getRemembableColumns()
    {
        return $this->lookupModel()->getColumns();
    }

    public function getRememberColumns() {
        if ( ! $this->settingable)
            return null;

        $user = $this->getUser();

        if ( ! $user)
            return null;

        $key = class_basename(get_class($this)) . "_" . $user->id;
        return settings($key);
    }

    public function rememberColumns($columns) {
        if ( ! $this->settingable)
            return;

        $user = $this->getUser();

        if ( ! $user)
            return;

        $key = class_basename(get_class($this)) . "_" . $user->id;
        settings($key, $columns);
    }

    public function hasExportableColumns()
    {
        return true;
    }

    public function hasPrintableColumns()
    {
        return true;
    }

    public function getMainActions()
    {
        $actions = [];

        if ($this->settingable)
            $actions[] = [
                'title' => trans('front.settings'),
                'url'   => $this->getRoute('edit'),
                'modal' => $this->getTableId() . "SettingsModal"
            ];

        if ($this->hasPrintableColumns()) {
            $actions[] =  [
                'title' => 'CSV',
                'url'   => $this->getRoute('csv'),
                'onClick' => "dataTableExport(event, 'csv')"
            ];
        }

        if ($this->hasExportableColumns()) {
            $actions[] = [
                'title' => 'Excel',
                'url'   => $this->getRoute('excel'),
                'onClick' => "dataTableExport(event, 'excel')"
            ];

            $actions[] = [
                'title' => 'PDF',
                'url'   => $this->getRoute('pdf'),
                'onClick' => "dataTableExport(event, 'pdf')"
            ];
        }

        return $actions;
    }

    protected function extraQuery($query)
    {
        return $query;
    }

    protected function getAjaxParameters()
    {
        return [
            'url' => $this->getRoute('data'),
            'cache' => false,
            'type' => 'GET',
            'data' => 'function(d) { d.key = "value"; }',
        ];
    }

    protected function getBuilderParameters()
    {
        $params = [
            'processing' => true,
            'serverSide' => true,
            'language' => [
                'processing' => '<i class="loader"></i>',
                'paginate' => [
                    'next' => '»',
                    'previous' => '«'
                ],
                'lengthMenu' => "_MENU_",
                'search' => trans("front.search") . ":",
            ],
            'dom' => '<"top" Bf><"table-responsive" rt><"bottom" pl><"clear">',
            'lengthMenu' => [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            'classes' => ['sLengthSelect' => 'form-control'],
        ];

        if ($order = $this->getDefaultOrder()) {
            $columnIndex = $this->columnNameIntoIndex($order[0]);

            if ($columnIndex !== null) {
                $params['order'] = [$columnIndex, $order[1]];
            }
        }

        return $params;
    }

    /**
     * @return null|array
     */
    protected function getDefaultOrder()
    {
        return null;
    }

    protected function columnNameIntoIndex(string $name)
    {
        $index = 0;

        foreach ($this->getColumns() as $column => $data) {
            if ($column === $name) {
                return $index;
            }

            $index++;
        }

        return null;
    }

    protected function getRelations()
    {
        $columns = $this->getColumns();

        $relations = [];

        foreach ($columns as $column) {
            if ( ! empty($column['relations'])) {
                if (is_array($column['relations']))
                    $relations = array_merge($relations, $column['relations']);
                elseif (is_string($column['relations'])) {
                    $relations[] = $column['relations'];
                }
            }

            $parts = explode('.', $column['name']);

            if (count($parts) < 2)
                continue;

            $relations[] = $parts[0];
        }

        return $relations;
    }

    protected function hasRowActions() {
        return true;
    }

    protected function renderColumn($model, $column) {
        if ($this->isHtmlBuild())
            return $this->lookupModel()->renderHtml($model, $column['data']);

        return $this->lookupModel()->render($model, $column['data']);
    }

    protected function renderRowActions($actions) {
        return view('front::Lookup.partials.actions', ['actions' => $actions]);
    }

    protected function renderMainActions($actions) {
        return view('front::Lookup.partials.actions', ['actions' => $actions])->render();
    }
}