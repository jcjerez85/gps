<?php

namespace App\Http\Controllers\Admin;

use Tobuli\Services\DocsService;

class PagesController extends BaseController
{
    private $docsService;

    public function __construct(DocsService $docsService)
    {
        parent::__construct();

        $this->docsService = $docsService;
    }

    public function index()
    {
        return $this->edit('privacy_policy');
    }

    public function edit(string $type)
    {
        try {
            $content = $this->docsService->get($type);
        } catch (\InvalidArgumentException $e) {
            abort(404);
        }

        $docTypes = $this->docsService->getTypes();

        return view('admin::Docs.edit', compact('docTypes', 'type', 'content'));
    }

    public function update(string $type)
    {
        try {
            $content = $this->docsService->store($type, request('content'));
        } catch (\InvalidArgumentException $e) {
            abort(404);
        }

        return redirect()->back()->with(compact('content'));
    }

}
