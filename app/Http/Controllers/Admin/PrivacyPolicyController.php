<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\File;

class PrivacyPolicyController extends BaseController
{

    public function create()
    {
        $file = storage_path('privacy_policy.txt');

        return view('admin::PrivacyPolicy.create', [
            'content' => File::exists($file) ? File::get($file) : null
        ]);
    }

    public function store()
    {
        $content = request('content');

        File::put(storage_path('privacy_policy.txt'), $content);

        return redirect()->back()->with(compact('content'));
    }

}
