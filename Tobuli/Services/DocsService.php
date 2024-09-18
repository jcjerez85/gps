<?php

namespace Tobuli\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocsService
{
    protected $list = [];

    public function __construct()
    {
        $this->list = [
            'privacy_policy' => [
                'title' => trans('admin.privacy_policy'),
                'path'  => 'privacy_policy.txt'
            ],
            'terms_conditions' => [
                'title' => trans('admin.terms_conditions'),
                'path'  => 'terms_conditions.txt'
            ],
            'delete_my_account' => [
                'title' => trans('admin.delete_my_account'),
                'path'  => 'delete_my_account.txt'
            ],
        ];
    }

    public function getTypes(): array
    {
        return $this->list;
    }

    public function get(string $type): string
    {
        $path = $this->getPath($type);

        try {
            return File::get($path);
        } catch (FileNotFoundException $e) {
            return '';
        }
    }

    public function store(string $type, string $content)
    {
        $path = $this->getPath($type);

        return File::put($path, $content);
    }

    /**
     * @throws NotFoundHttpException
     * @return string
     */
    private function getPath(string $type): string
    {
        if (!isset($this->list[$type])) {
            throw new NotFoundHttpException($type . ' type does not exist');
        }

        return storage_path($this->list[$type]['path']);
    }
}