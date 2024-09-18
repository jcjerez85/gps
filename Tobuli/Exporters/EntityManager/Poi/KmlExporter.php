<?php

namespace Tobuli\Exporters\EntityManager\Poi;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tobuli\Exporters\Downloader\AbstractKmlChunkDownloader;
use Tobuli\Exporters\EntityManager\ExporterInterface;

class KmlExporter extends AbstractKmlChunkDownloader implements ExporterInterface
{
    public function generateReport(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return $this->setChunkSize(300)->download($query, $filename);
    }

    protected function writePlacemark($item)
    {
        $this->xmlWriter->startElement('Placemark');

        $this->xmlWriter->writeElement('name', $item->name);
        $this->xmlWriter->writeElement('description', $item->description);

        $this->xmlWriter->startElement('Point');
        $this->xmlWriter->writeElement('coordinates', $item->coordinates['lng'] . ',' . $item->coordinates['lat']);
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement('Style');
        $this->xmlWriter->startElement('IconStyle');
        $this->xmlWriter->startElement('Icon');
        $this->xmlWriter->writeElement('href', \URL::to('/') . '/' . $item->mapIcon->path);
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();

        $this->xmlWriter->endElement();
    }
}