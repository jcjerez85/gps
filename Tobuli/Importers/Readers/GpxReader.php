<?php

namespace Tobuli\Importers\Readers;

use Symfony\Component\HttpFoundation\File\File;

abstract class GpxReader extends Reader
{
    const KEY_WPT = 'wpt';

    public function supportsFile(File $file): bool
    {
        return !empty($this->read($file));
    }

    protected function getData($file)
    {
        try {
            $xml = simpleXML_load_file($file, "SimpleXMLElement", LIBXML_NOCDATA);
            $data = $this->simpleXmlToArray($xml);
        } catch (\Exception $e) {
            return null;
        }

        return $data;
    }
}