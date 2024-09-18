<?php

namespace Tobuli\Exporters\Downloader;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractKmlChunkDownloader extends AbstractChunkDownloader
{
    protected $xmlWriter;
    
    public function __construct()
    {
        $this->xmlWriter = new \XMLWriter();
    }

    public function download(Builder $query, string $filename): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->generateFile($query, $filename),
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '";',
            ]
        );
    }

    public function generateFile(Builder $query, string $filename): string
    {
        $path = storage_path('cache/' . $filename);

        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0', 'UTF-8');
        $this->xmlWriter->startElementNs(null, 'kml', 'http://earth.google.com/kml/2.2');
        $this->xmlWriter->startElement('Document');

        $query->chunk($this->chunkSize, function ($items) use ($path) {
            foreach ($items as $item) {
                $this->writePlacemark($item);
            }

            file_put_contents($path, $this->xmlWriter->flush(true), FILE_APPEND);
        });

        $this->xmlWriter->endElement(); // Document
        $this->xmlWriter->endElement(); // kml

        file_put_contents($path, $this->xmlWriter->flush(true), FILE_APPEND);

        return $path;
    }

    abstract protected function writePlacemark($item);
}