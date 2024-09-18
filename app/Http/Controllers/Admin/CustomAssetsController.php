<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class CustomAssetsController extends Controller
{
    public function getCustomAsset($asset)
    {
        $assetFile = $this->whichAssetFile($asset);

        $content = File::exists($assetFile) ? File::get($assetFile) : '';

        return view('admin::CustomAssets.' . $asset )->with([
            'content' => $content
        ]);
    }

    public function setCustomAsset(Request $request, $asset)
    {
        $assetFile = $this->whichAssetFile($asset);

        $content = $request->filled('content') ? $request->input('content') : '';

        $this->checkIfDirectoryExists();

        File::put($assetFile, $content);

        return view('admin::CustomAssets.' . $asset )->with([
            'content' => $content
        ]);
    }

    public function whichAssetFile($asset)
    {
        if ($asset === 'js' && $this->user->isAdmin()) {
            return storage_path('custom/js.js');
        } elseif ($asset === 'css') {
            $prefix = $this->user->isReseller() ? $this->user->id : '';
            return storage_path("custom/css{$prefix}.css");
        } else {
            throw new RouteNotFoundException();
        }
    }

    public function checkIfDirectoryExists()
    {
        if (!File::isDirectory(storage_path('custom'))) {
            File::makeDirectory(storage_path('custom'));
        }
    }
}

