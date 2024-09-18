<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class PrivacyPolicyController extends Controller
{
    public function index()
    {
        $file = storage_path('privacy_policy.txt');

        return view('front::PrivacyPolicy.index', [
            'content' => File::exists($file) ? File::get($file) : null
        ]);
    }
}
