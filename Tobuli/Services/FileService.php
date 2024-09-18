<?php

namespace Tobuli\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    /**
     * Save file to directory
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return string File path
     * @throws \Exception
     */
    public function save(UploadedFile $file, $directory, $filename = null)
    {
        $this->createDirectory($directory);

        if (! $filename) {
            $extension = $file->getClientOriginalExtension();
            $filename = $this->generateFilename($directory, $extension);
        }

        $file = $file->move($directory, $filename);

        if (! $file) {
            throw new \Exception(trans('global.failed_file_save'));
        }

        return $file->getPathname();
    }

    /**
     * Create file's directory if it doesn't exist
     *
     * @param string $directory
     * @return void
     * @throws Exception
     */
    public function createDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            return;
        }

        if (! File::makeDirectory($directory, 0755, true)) {
            throw new \Exception('Failed creating directory');
        }
    }

    /**
     * Touch saved file to set timestamps
     *
     * @param string|\SplFileInfo $file
     * @param int $timestamp
     * @return boolean
     */
    public function setFileTimestamps($file, $timestamp)
    {
        return touch(
            $this->parseFilePath($file),
            $timestamp
        );
    }

    /**
     * Parse file path
     *
     * @param string|\SplFileInfo $file
     * @return string
     */
    private function parseFilePath($file)
    {
        if (is_string($file) && File::isFile($file)) {
            return $file;
        }

        if ($file instanceof \SplFileInfo) {
            return $file->getPathname();
        }

        throw new \Exception('Failed parsing file\'s path');
    }

    /**
     * Create random filename
     *
     * @param string $directory
     * @param string $extension
     * @return string
     */
    public function generateFilename(string $directory, string $extension): string
    {
        $directory = Str::finish($directory, '/');

        do {
            $name = Str::finish(Str::random(), '.') . $extension;
        } while (File::exists($directory . $name));

        return $name;
    }
}
