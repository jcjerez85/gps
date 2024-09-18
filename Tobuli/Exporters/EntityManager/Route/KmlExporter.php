<?php

namespace Tobuli\Exporters\EntityManager\Route;

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

        $this->xmlWriter->startElement('Style');
        $this->xmlWriter->startElement('LineStyle');
        $this->xmlWriter->writeElement('color', $item->color);
        $this->xmlWriter->writeElement('width', '7');
        $this->xmlWriter->endElement();
        $this->xmlWriter->endElement();

        $this->xmlWriter->startElement('LineString');
        $this->xmlWriter->writeElement('altitudeMode', 'absolute');
        $this->xmlWriter->startElement('coordinates');

        foreach ($item->coordinates as $coordinates) {
            $this->xmlWriter->text($coordinates['lng'] . ',' . $coordinates['lat'] . ' ');
        }

        $this->xmlWriter->endElement(); // coordinates
        $this->xmlWriter->endElement(); // LineString
        $this->xmlWriter->endElement(); // Placemark
    }
}