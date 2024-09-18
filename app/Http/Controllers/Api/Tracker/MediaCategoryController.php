<?php

namespace App\Http\Controllers\Api\Tracker;

use Symfony\Component\HttpFoundation\Response;
use Tobuli\Entities\MediaCategory;

class MediaCategoryController extends ApiController
{
    public function getList(): Response
    {
        return response()->json([
            'status' => 1,
            'data'   => MediaCategory::usersAccessible($this->deviceInstance->users)->get()->map(function($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->title,
                ];
            })
        ]);
    }
}
