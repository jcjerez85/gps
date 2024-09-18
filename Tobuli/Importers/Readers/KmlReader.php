<?php

namespace Tobuli\Importers\Readers;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

abstract class KmlReader extends Reader
{
    const KEY_FOLDER = 'Folder';
    const KEY_PLACEMARK = 'Placemark';
    const KEY_POINT = 'Point';
    const KEY_POLYGON = 'Polygon';
    const KEY_MULTIGEOMETRY = 'MultiGeometry';
    const KEY_POLYLINE = 'LineString';
    const KEY_NAME = 'name';
    const KEY_STYLE = 'Style';
    const KEY_NORMAL = 'normal';
    const KEY_STYLE_MAP = 'StyleMap';

    protected $styles;
    protected $styleMap;

    public function supportsFile(File $file): bool
    {
        return $this->isValidFormat($file);
    }

    public function isValidFormat($file)
    {
        $data = $this->getData($file);

        if (is_null($data)) {
            return false;
        }

        $placemarks = $this->parseElement($data, self::KEY_PLACEMARK);

        if (empty($placemarks)) {
            return false;
        }

        return true;
    }

    protected function getData($file)
    {
        try {
            $xml = simpleXML_load_file($file, "SimpleXMLElement", LIBXML_NOCDATA);
            $data = $this->simpleXmlToArray($xml);
        } catch (\Exception $e) {
            return null;
        }

        $this->setStyles($data);

        return $data;
    }

    protected function setStyles($data)
    {
        $styles = $this->parseElement($data, self::KEY_STYLE);
        $this->parseStyles($styles);
        $styleMap = $this->parseElement($data, self::KEY_STYLE_MAP);
        $this->parseStyleMap($styleMap);
    }

    protected function parseStyles($styles)
    {
        $result = [];

        if ($styles) {
            foreach ($styles as $style) {
                $style = $this->simpleXMLElementToArray($style);

                if (isset($style['id'])) {
                    if (isset($style['color'])) {
                        if ( ! Str::startsWith($style['color'], '#')) {
                            $result[$style['id']]['color'] = '#' . $style['color'];
                        }

                        $result[$style['id']]['color'] = substr($result[$style['id']]['color'], 0, 7);
                    }

                    if (isset($style['href'])) {
                        $result[$style['id']]['icon'] = $style['href'];
                    }
                }
            }
        }

        $this->styles = $result;
    }

    protected function parseStyleMap($data)
    {
        $result = [];
        $data = isset($data['@attributes']) ? [$data] : $data;

        foreach ($data as $styleMap) {
            $id = $styleMap['@attributes']['id'] ?? null;

            if (isset($id)) {
                $value = null;

                if (isset($styleMap['Pair'])) {
                    foreach ($styleMap['Pair'] as $pair) {
                        if (isset($pair['key']) && $pair['key'] == self::KEY_NORMAL) {
                            $value = $pair['styleUrl'];
                        }
                    }
                }

                if (isset($value)) {
                    $result[$id] = $value;
                }
            }
        }

        $this->styleMap = $result;
    }

    protected function applyStyles($data, $fields, $renameFields = [])
    {
        $result = $data;

        $style = null;

        if (isset($data[self::KEY_STYLE])) {
            foreach ($fields as $field) {
                $value = $this->search($data[self::KEY_STYLE], $field);

                if (is_null($value))
                    continue;

                $style[$field] = $value;
            }
        }

        if (isset($data['styleUrl'])) {
            $style = $this->getStyle($data['styleUrl'], $fields);
            unset($result['styleUrl']);
        }

        if (in_array('color', $fields) && empty($style) && empty($data['color'])) {
            $style['color'] = '#ffffff';
        }

        if ($style) {
            if ($renameFields) {
                foreach ($renameFields as $old => $new) {
                    if (isset($style[$old])) {
                        $style[$new] = $style[$old];
                        unset($style[$old]);
                    }
                }
            }

            $result = array_merge($result, $style);
        }

        return $result;
    }

    protected function getStyle($id, $fields)
    {
        $result = [];
        $id = str_replace('#', '', $id);

        if (isset($this->styles[$id])) {
            foreach ($fields as $field) {
                if ( ! isset($this->styles[$id][$field])) {
                    continue;
                }

                $result[$field] = $this->styles[$id][$field];
            }

            return $result;
        }

        if (isset($this->styleMap[$id])) {
            $newId = str_replace('#', '', $this->styleMap[$id] ?? null) ?? null;

            if (isset($this->styles[$newId])) {
                foreach ($fields as $field) {
                    $style = $this->styles[$newId][$field] ?? null;

                    if (is_null($style)) {
                        continue;
                    }

                    $result[$field] = $style;
                }
            }
        }

        return $result;
    }

    protected function search($data, $field)
    {
        foreach ($data as $key => $value) {
            if ($key === $field)
                return $value;

            if ( ! is_array($value))
                continue;

            $result = $this->search($value, $field);

            if ( ! is_null($result))
                return $result;
        }

        return null;
    }
}