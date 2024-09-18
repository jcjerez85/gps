<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\ServiceProvider;

class QueryBuilderMacrosProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        QueryBuilder::macro('clearOrdersBy', function () {
            $this->{$this->unions ? 'unionOrders' : 'orders'} = null;

            return $this;
        });

        EloquentBuilder::macro("clearOrdersBy", function () {
            $query = $this->getQuery();

            $query->{$query->unions ? 'unionOrders' : 'orders'} = null;

            return $this;
        });

        QueryBuilder::macro("toRaw", function () {

            $sql = vsprintf(str_replace('?', '%s', $this->toSql()), collect($this->getBindings())->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray());

            return $sql;
        });

        EloquentBuilder::macro("toRaw", function () {
            return $this->getQuery()->toRaw();
        });

        EloquentBuilder::macro('isJoined', function ($table) {
            $query = $this->getQuery();

            if ($query->joins == null) {
                return false;
            }

            foreach ($query->joins as $join) {
                if ($join->table == $table) {
                    return true;
                }
            }

            return false;
        });

        EloquentBuilder::macro('toPaginator', function (int $limit, string $sortCol, string $sortDir): LengthAwarePaginator {
            $query = $this->orderBy($sortCol, $sortDir);

            $items = $query->paginate($limit);

            if ($items->currentPage() > $items->lastPage()) {
                $items = $query->paginate($limit, ['*'], 'page', 1);
            }

            $items->sorting = ['sort_by' => $sortCol, 'sort' => $sortDir];

            return $items;
        });

        EloquentBuilder::macro('reversePaginate', function (int $limit = null) {
            $items = $this->paginate($limit);

            return $items->setCollection($items->getCollection()->reverse()->values());
        });

        EloquentBuilder::macro('whereManagerOwn', function (string $column, $manager) {
            return $this->getQuery()->whereIn($column, function ($query) use ($manager) {
                $query
                    ->select('users.id')
                    ->from('users')
                    ->where('users.id', $manager->id)
                    ->orWhere('users.manager_id', $manager->id)
                ;
            });
        });

        BelongsToMany::macro('attachQuery', function ($query) {
            /** @var BelongsToMany $this */

            switch (true) {
                case $query instanceof EloquentBuilder:
                    $model = $query->getModel();
                    break;
                case is_subclass_of($query, Relation::class):
                    $model = $query->getQuery()->getModel();
                    break;
                default:
                    throw new \RuntimeException("Class must implement EloquentBuilder or Relation");
            }

            $queryClass = get_class($model);
            $macroClass = get_class($this->getRelated());
            if ($macroClass !== $queryClass) {
                throw new \RuntimeException("The related object classes do not match: $macroClass and $queryClass");
            }

            $tablePivot = $this->getTable();
            $foreignPivotKey = $this->getForeignPivotKeyName();
            $relatedPivotKey = $this->getRelatedPivotKeyName();

            $relatedKey = $this->getRelated()->getKeyName();
            $relatedTable = $this->getRelated()->getTable();
            $relatedQualifiedKey = "$relatedTable.$relatedKey";

            $select = $query
                ->clearOrdersBy()
                ->select(\DB::raw("{$this->getParent()->id} AS `$foreignPivotKey`, $relatedQualifiedKey"))
                ->leftJoin("$tablePivot AS tmp_table_pivot", function ($join) use ($relatedPivotKey, $foreignPivotKey, $relatedQualifiedKey){
                    $join->on("tmp_table_pivot.$relatedPivotKey", '=', $relatedQualifiedKey)
                        ->where("tmp_table_pivot.$foreignPivotKey", '=', $this->getParent()->id);
                })
                ->whereNull("tmp_table_pivot.$relatedPivotKey")
            ;

            $insert = "INSERT INTO `$tablePivot` (`$foreignPivotKey`, `$relatedPivotKey`) ";

            $bindings = $select->getBindings();
            $insertQuery = str_finish($insert, ' ') . $select->toSql();

            \DB::insert($insertQuery, $bindings);
        });

        BelongsToMany::macro('detachQuery', function ($query) {

            $tablePivot = $this->getTable();
            $foreignPivotKey = $this->getForeignPivotKeyName();
            $relatedPivotKey = $this->getRelatedPivotKeyName();

            $relatedKey = $this->getRelated()->getKeyName();
            $relatedTable = $this->getRelated()->getTable();
            $relatedQualifiedKey = "$relatedTable.$relatedKey";

            $sql = $query->select($relatedQualifiedKey)->toRaw();

            \DB::table($tablePivot)
                ->where("$tablePivot.$foreignPivotKey", $this->getParent()->id)
                ->join(\DB::raw('(' . $sql. ') AS tmp_table_related'), function($join) use ($relatedKey, $tablePivot, $relatedPivotKey){
                    $join->on("tmp_table_related.$relatedKey", '=', "$tablePivot.$relatedPivotKey");
                })
                ->whereNotNull("tmp_table_related.$relatedKey")
                ->delete();
        });

        BelongsToMany::macro('syncLoader', function ($loader) {
            if (!$loader->hasSelect())
                return;

            if ($loader->getDetach()) {
                $this->detachQuery($loader->getDetach());
            }

            if ($loader->getAttach()) {
                $this->attachQuery($loader->getAttach());
            }
        });
    }
}
