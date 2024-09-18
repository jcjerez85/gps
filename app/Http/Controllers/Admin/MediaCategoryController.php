<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ResourseNotFoundException;
use CustomFacades\Validators\MediaCategoryValidator;
use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\User;

class MediaCategoryController extends BaseController
{
    public function index()
    {
        $items = MediaCategory::with('user:id,email')
            ->search(request()->get('search_phrase'))
            ->paginate(15);

        return view('admin::MediaCategories.' . (request()->ajax() ? 'table' : 'index'), [
            'items' => $items,
        ]);
    }

    public function create()
    {
        $users = User::query()->pluck('email', 'id')->prepend(trans('admin.select'), '');

        return view('admin::MediaCategories.create')->with(['users' => $users]);
    }

    public function store()
    {
        $data = $this->data;
        
        MediaCategoryValidator::validate('create', $data);

        MediaCategory::create($data);

        return response()->json(['status' => 1]);
    }

    public function edit($id)
    {
        $category = MediaCategory::find($id);

        if (!$category) {
            throw new ResourseNotFoundException(trans('front.media_category'));
        }

        $users = User::query()->pluck('email', 'id')->prepend(trans('admin.select'), '');

        return view('admin::MediaCategories.edit', [
            'item' => $category,
            'users' => $users,
        ]);
    }

    public function update()
    {
        $data = $this->data;

        MediaCategoryValidator::validate('update', $data);

        $category = MediaCategory::find($data['id']);

        $category->update($data);

        return response()->json(['status' => 1]);
    }

    public function destroy()
    {
        $category = MediaCategory::find($this->data['id'] ?? null);

        if (!$category) {
            throw new ResourseNotFoundException(trans('front.media_category'));
        }

        $category->delete();

        return response()->json(['status' => 1]);
    }
}
