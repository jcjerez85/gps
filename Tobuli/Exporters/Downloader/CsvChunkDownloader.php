<?php

namespace Tobuli\Exporters\Downloader;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Tobuli\Exporters\Util\ParseValueTrait;

class CsvChunkDownloader extends AbstractChunkDownloader
{
    use ParseValueTrait;

    private $delimiter;
    private $enclosure;

    public function __construct(int $chunkSize = 500, string $delimiter = ',', string $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;

        parent::__construct($chunkSize);
    }

    public function download(Builder $query, array $attributes, string $filename): BinaryFileResponse
    {
        return new BinaryFileResponse(
            $this->generateFile($query, $attributes, $filename),
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '";',
            ]
        );
    }

    public function generateFile(Builder $query, array $attributes, string $filename): string
    {
        $path = storage_path('cache/' . $filename);

        $file = fopen($path, 'wb');

        // UTF-8 BOM
        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, $attributes);

        $query->chunk($this->chunkSize, function ($items) use ($file, $attributes) {
            foreach ($items as $item) {
                fputcsv($file, $this->parseValues($item, $attributes), $this->delimiter, $this->enclosure);
            }
        });

        fclose($file);

        return $path;
    }
}