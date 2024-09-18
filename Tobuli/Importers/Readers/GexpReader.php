<?php

namespace Tobuli\Importers\Readers;

use Symfony\Component\HttpFoundation\File\File;

abstract class GexpReader extends Reader
{
    protected $groups = [];

    public function supportsFile(File $file): bool
    {
        return !empty($this->read($file));
    }

    public function read($file)
    {
        $content = file_get_contents($file);
        $content = json_decode($content, true);

        if (empty($content)) {
            return null;
        }

        if ( ! empty($content['groups'])) {
            foreach ($content['groups'] as $group) {
                if ( ! isset($group['id'])) {
                    continue;
                }

                if (empty($group['title'])) {
                    continue;
                }

                $this->groups[$group['id']] = $group['title'];
            }
        }

        if (empty($content['geofences'])) {
            return null;
        }

        $rows = [];

        foreach ($content['geofences'] as $point) {
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