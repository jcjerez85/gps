<?php

namespace Tobuli\Importers\Readers;

abstract class Reader implements ReaderInterface
{
    const KEY_COORDINATES = 'coordinates';

    /**
     * @var array
     */
    protected $requiredFieldRules;

    protected function validateRequiredFields($fieldNames)
    {
        return true;
    }

    public function isValidFormat($file)
    {
        $rows = $this->read($file);

        if (empty($rows)) {
            return false;
        }

        return true;
    }

    protected function simpleXMLElementToArray($element)
    {
        $arr = (array)$element;

        foreach ($arr as $key => $arrElement) {
            if ($arrElement instanceof \SimpleXMLElement || is_array($arrElement)) {
                if ($arrElement instanceof \SimpleXMLElement && $arrElement->attributes()) {
                    $attribute = $this->parseXMLAttribute($arrElement);

                    if ($attribute) {
                        $arr = array_replace($arr, $attribute);
                    }

                    unset($arr[$key]);
                } elseif (is_array($arrElement) && isset($arrElement['@attributes'])) {
                    $name = $arrElement['@attributes']['name'] ?? null;
                    $value = $arrElement['value'] ?? null;

                    if ($name !== null && $value !== null) {
                        $arr = array_replace($arr, [$name => $value]);
                    }

                    unset($arr[$key]);
                } else {
                    $arr[$key] = $this->simpleXMLElementToArray($arrElement);
                }
            }

            if (isset($arr[$key]) && is_array($arr[$key])) {
                $arr = array_merge($arr, $arr[$key]);
                unset($arr[$key]);
            }
        }

        return $arr;
    }

    private function parseXMLAttribute($data)
    {
        $result = null;
        $name = ((array)$data->attributes()->name)[0] ?? null;
        $val = ((array)$data)['value'] ?? null;

        if (!is_null($name) && !is_null($val)) {
            $result = [$name => $val];
        }

        return $result;
    }

    protected function simpleXmlToArray($data)
    {
        $arr = (array)$data;

        foreach ($arr as $key => $arrElement) {
            if ($arrElement instanceof \SimpleXMLElement || is_array($arrElement)) {
                $arr[$key] = $this->simpleXmlToArray($arrElement);
            }
        }

        return $arr;
    }

    protected function parseElement($array, $elementKey)
    {
        if (!is_array($array)) {
            return [];
        }

        $result = [];

        foreach ($array as $key => $element) {
            if ($key === $elementKey) {
                $result = $element;
                break;
            }

            if (is_array($element)) {
                $tmp = $this->parseElement($element, $elementKey);

                if ($tmp) {
                    $result = $tmp;
                    break;
                }
            }
        }

        return $result;
    }

    protected function parseCoordinates($data)
    {
        $coordinates = [];

        $coords = trim(preg_replace('/\r\n|\r|\n|\t+/', '', $data));

        if ($coords) {
            $coords = explode(',', $coords);

            if (isset($coords[0]) && isset($coords[1])) {
                $coordinates = [
                    'lat' => trim($coords[1]),
                    'lng' => trim($coords[0]),
                ];
            }
        }

        return $coordinates;
    }
}
