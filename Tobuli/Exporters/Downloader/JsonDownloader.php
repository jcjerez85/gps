<?php

namespace Tobuli\Exporters\Downloader;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class JsonDownloader
{
    public function download(array $data, string $filename, int $flags = JSON_UNESCAPED_UNICODE): BinaryFileResponse
    {
        $path = storage_path('cache/' . $filename);

        \File::put($path, \json_encode($data, $flags));

        return \Response::download($path);
    }
}