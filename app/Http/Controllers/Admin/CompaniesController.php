<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use CustomFacades\Validators\CompanyValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Tobuli\Entities\Company;

class CompaniesController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->all();

        $sort = $input['sorting'] ?? ['sort_by' => 'name', 'sort' => 'asc'];

        $items = Company::userAccessible($this->user)
            ->search($input['search_phrase'] ?? null)
            ->filter($input)
            ->toPaginator($input['limit'] ?? 50, $sort['sort_by'], $sort['sort']);

        return $this->api
            ? $items
            : View::make('admin::Companies.' . ($request->ajax() ? 'table' : 'index'))
                ->with(compact('items'));
    }
    
    public function create()
    {
        return View::make('admin::Companies.create');
    }
    
    public function store(Request $request)
    {
        CompanyValidator::validate('write', $request->all());

        $company = new Company($request->all());
        $company->owner()->associate($this->user);
        $company->save();
        
        return new JsonResponse(['status' => 1]);
    }
    
    public function show(int $id)
    {
        return $this->getItem($id);
    }

    public function edit(int $id = null)
    {
        $item = $this->getItem($id);

        return View::make('admin::Companies.edit')->with(compact('item'));
    }
    
    public function update(Request $request, int $id = null)
    {
        CompanyValidator::validate('write', $request->all());

        $item = $this->getItem($id ?: $request->get('id'));
        $item->fill($request->all());

        $item->save();

        return new JsonResponse(['status' => 1]);
    }
    
    public function destroy(Request $request, int $id = null)
    {
        $ids = (array)($id ?: $request->get('id'));

        Company::userAccessible($this->user)->whereIn('id', $ids)->delete();
        
        return new JsonResponse(['status' => 1]);
    }

    private function getItem($id)
    {
        return Company::userAccessible($this->user)->findOrFail($id);
    }
}