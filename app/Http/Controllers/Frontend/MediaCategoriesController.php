<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Auth;
use CustomFacades\Validators\MediaCategoryValidator;
use Tobuli\Entities\MediaCategory;
use Tobuli\Exceptions\ValidationException;

class MediaCategoriesController extends Controller
{
    public function index()
    {
        return $this->getList('index');
    }

    public function table()
    {
        return $this->getList('table');
    }

    private function getList(string $view)
    {
        $this->checkException('media_categories', 'view');

        $mediaCategories = MediaCategory::paginate();

        return view('front::MediaCategories.' . $view)->with(compact('mediaCategories'));
    }

    public function create()
    {
        $this->checkException('media_categories', 'store');

        return view('front::MediaCategories.create');
    }

    public function store()
    {
        $this->checkException('media_categories', 'store');

        $data = request()->all();

        $data['user_id'] = $this->user->id;
        MediaCategoryValidator::validate('create', $data);

        MediaCategory::create($data);

        return ['status' => 1];
    }

    public function edit(int $id)
    {
        $item = MediaCategory::findOrFail($id);

        $this->checkException('media_categories', 'edit', $item);

        return view('front::MediaCategories.edit')->with(compact('item'));
    }

    public function update(int $id): array
    {
        $item = MediaCategory::findOrFail($id);

        $this->checkException('media_categories', 'edit', $item);

        $data = request()->all();

        MediaCategoryValidator::validate('update', $data, $item->id);

        $success = $item->update($data);

        return ['status' => (int)$success];
    }

    public function doDestroy(int $id)
    {
        $item = MediaCategory::findOrFail($id);

        $this->checkException('media_categories', 'destroy', $item);

        return view('front::MediaCategories.destroy')->with(compact('item'));
    }

    public function destroy(int $id): array
    {
        $item = MediaCategory::findOrFail($id);

        $this->checkException('media_categories', 'destroy', $item);

        $success = $item->delete();

        return ['status' => (int)$success];
    }
}
