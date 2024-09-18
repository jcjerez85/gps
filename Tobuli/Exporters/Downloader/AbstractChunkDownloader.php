<?php

namespace Tobuli\Exporters\Downloader;

abstract class AbstractChunkDownloader
{
    protected $chunkSize;

    public function __construct(int $chunkSize = 500)
    {
        $this->chunkSize = $chunkSize;
    }

    public function setChunkSize(int $chunkSize): self
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }
}