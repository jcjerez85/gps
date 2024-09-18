<?php

namespace Tobuli\Exporters\Downloader;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Tobuli\Exporters\Util\ParseValueTrait;
use Tobuli\Exporters\Util\XlsxWriter;

class XlsxChunkDownloader extends AbstractChunkDownloader
{
    use ParseValueTrait;

    public function download(
        Builder $query,
        array $attributes,
        string $filename,
        string $sheetName = 'Sheet1'
    ): BinaryFileResponse {
        $path = storage_path('cache/' . $filename);

        $writer = new XlsxWriter();

        $writer->writeSheetHeader($sheetName, []);
        $writer->writeSheetRow($sheetName, $attributes);

        $query->chunk($this->chunkSize, function ($items) use ($writer, $attributes, $sheetName) {
            foreach ($items as $item) {
                $writer->writeSheetRow($sheetName, $this->parseValues($item, $attributes));
            }
        });

        $writer->writeToFile($path);

        return new BinaryFileResponse($path, Response::HTTP_OK, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '";',
        ]);
    }
}