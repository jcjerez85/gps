<?php

namespace Tobuli\Entities\File;

use App\Exceptions\ResourseNotFoundException;
use Carbon\Carbon;
use Crypt;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileQuery
{
    protected $model;
    protected $entity;
    protected $directory;
    protected $order;

    public function __construct(FileEntity $model, \Eloquent $entity = null)
    {
        $this->model = $model;
        $this->entity = $entity;
        $this->directory = $model->getDirectory($entity);
        $this->order = 'desc';
    }

    public function all()
    {
        $files = $this->getAllFilesInDir($this->directory);

        return $this->buildCollection($files);
    }

    public function find($filename)
    {
        $filename = Crypt::decrypt(urldecode($filename));

        $file = $this->directory . (Str::startsWith($filename, '/') ? '' : '/') . $filename;

        if ( ! File::exists($file))
            return null;

        return $this->newModelInstance($file);
    }

    public function paginate($limit = 15, FileSorter $fileSorter = null)
    {
        $page = request()->get('page', 1);
        $offset = $limit * ($page - 1);

        $data = ($fileSorter ?: $this->getEntityFileSorter())
            ->offset($offset)
            ->limit($limit)
            ->get();

        $files = $this->buildCollection($data['files']);

        return new LengthAwarePaginator($files, $data['total'], $limit, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }

    public function getEntityFileSorter(): FileSorter
    {
        $path = realpath($this->directory);

        if (!$path) {
            throw new ResourseNotFoundException("Device media directory doesn't exist.");
        }

        return new FileSorter($path);
    }

    public function orderByDate($order = 'desc')
    {
        if (in_array($order, ['asc', 'desc'])) {
            $this->order = $order;
        }

        return $this;
    }

    public function olderThan($days)
    {
        $files = [];

        $iterator = $this->getDirIterator();

        if (is_null($iterator)) {
            return $this->buildCollection($files);
        }

        foreach ($iterator as $object) {
            $fileDate = Carbon::createFromTimestamp($object->getMTime());

            if ($fileDate->diffInDays(Carbon::now()) > $days) {
                $files[] = $object;
            }
        }

        return $this->buildCollection($files);
    }

    public function findLatest()
    {
        $result = null;

        $iterator = $this->getDirIterator();

        if (is_null($iterator)) {
            return $result;
        }

        foreach ($iterator as $object) {
            if (! is_null($result) && $object->getMTime() < $result->getMTime()) {
                continue;
            }

            $result = $object;
        }

        if (!is_null($result)) {
            $result = $this->newModelInstance($result);
        }

        return $result;
    }

    private function buildCollection($files)
    {
        $collection = new Collection();

        foreach ($files as $file) {
            $instance = $this->newModelInstance($file);
            $collection->push($instance);
        }

        return $collection;
    }

    private function newModelInstance($file)
    {
        return new $this->model($file);
    }

    private function getAllFilesInDir($dir)
    {
        return File::isDirectory($dir) ? File::allFiles($this->directory) : [];
    }

    private function getDirIterator($path = null)
    {
        $path = $path ?? $this->directory;
        $path = realpath($path);

        if (empty($path) || ! file_exists($path)) {
            return null;
        }

        $dirIterator = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($dirIterator);

        return $iterator;
    }

    public function getDirectorySize() {
        $total = 0;
        $iterator = $this->getDirIterator();

        if (is_null($iterator)) {
            return $total;
        }

        foreach ($iterator as $object) {
            $total += $object->getSize();
        }

        return $total;
    }
}
