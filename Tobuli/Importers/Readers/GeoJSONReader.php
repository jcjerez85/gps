<?php

namespace Tobuli\Importers\Readers;

use Symfony\Component\HttpFoundation\File\File;

abstract class GeoJSONReader extends Reader
{
    const KEY_STYLE = 'style';
    const KEY_PROPERTIES = 'properties';

    public function supportsFile(File $file): bool
    {
        return !empty($this->read($file));
    }

    public function read($file)
    {
        $content = file_get_contents($file);
        $content = json_decode($content, true);
        $content = $content['features'] ?? null;

        if (empty($content)) {
            return null;
        }

        $rows = [];

        foreach ($content as $point) {
            $data = $this->parsePoint($point);

            if (is_null($data)) {
                continue;
            }

            $rows[] = $data;
        }

        return $rows;
    }

    abstract protected function parsePoint($point);
}