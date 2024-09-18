<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Tobuli\Services\DocsService;

class PagesController extends Controller
{
    private $docsService;

    public function __construct(DocsService $docsService)
    {
        parent::__construct();

        $this->docsService = $docsService;
    }

    public function show(string $type)
    {
        $content = $this->docsService->get($type);

        return view('front::Docs.show', compact('content'));
    }
}
