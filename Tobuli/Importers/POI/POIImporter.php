<?php

namespace Tobuli\Importers\POI;

use CustomFacades\Repositories\PoiRepo;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tobuli\Entities\MapIcon;
use Tobuli\Entities\PoiGroup;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Importers\Importer;

class POIImporter extends Importer
{
    protected $defaults = ['active' => 1];

    protected $icons = [];

    protected $file;

    protected function importItem($data, $additionals = [])
    {
        $data = $this->mergeDefaults($data);
        $data = $this->setUser($data, $additionals);
        $data = $this->manageIcon($data, $additionals);

        if ( ! $this->validate($data)) {
            return;
        }

        $data = $this->normalize($data);

        if ($this->getPOI($data)) {
            return;
        }

        $this->create($data);
    }

    private function manageIcon($data, $additionals)
    {
        $result = $data;

        $result['map_icon_id'] = Arr::get($additionals, 'map_icon_id');

        if (isset($data['icon']) && $map_icon_id = $this->downloadIcon($data['icon'])) {
            $result['map_icon_id'] = $map_icon_id;
        }

        unset($result['icon']);

        return $result;
    }

    private function downloadIcon($url)
    {
        //tmp disable
        return null;

        $path = 'images/map_icons';
        $destination = public_path($path);
        $filename = sha1($url) . '.' . pathinfo($url, PATHINFO_EXTENSION);
        $url_hash = sha1($url);
        $existing = glob($destination . "/$url_hash.*");

        if ( ! empty($existing)) {
            if (isset($this->icons[$url_hash])) {
                return $this->icons[$url_hash];
            }

            $icon = MapIcon::where('path', $path . "/$filename")->first();

            if ( ! is_null($icon)) {
                $this->icons[$url_hash] = $icon->id;

                return $icon->id;
            }
        }

        $result = null;

        try {
            $image = file_get_contents($url, false, stream_context_create([
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ]));
        } catch (\Exception $e) {
            if ($this->stop_on_fail) {
                throw new ValidationException(['icon' => $e->getMessage()]);
            }

            $image = null;
        }

        if ($image) {
            $filePath = Str::finish($destination, '/') . $filename;

            if (file_put_contents($filePath, $image) !== false) {
                list($w, $h) = getimagesize($filePath);

                $mapIcon = MapIcon::create([
                    'path'   => Str::finish($path, '/') . $filename,
                    'width'  => $w,
                    'height' => $h,
                ]);

                $result = $mapIcon->id;
            }
        }

        return $result;
    }

    private function normalize(array &$data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'description'])) {
                $data[$key] = htmlspecialchars($value);
            }
        }

        return $data;
    }

    private function getPOI($data)
    {
        return PoiRepo::first(Arr::only($data, ['user_id', 'name', 'map_icon_id']));
    }

    private function create($data)
    {
        beginTransaction();
        try {
            if ( ! empty($data['group'])) {
                $this->createGroup($data);
            }

            PoiRepo::create($data);
        } catch (\Exception $e) {
            rollbackTransaction();
            throw $e;
        }
        commitTransaction();
    }

    private function createGroup(& $data)
    {
        $key = md5("{$data['user_id']}.{$data['group']}");

        $data['group_id'] = Cache::store('array')->rememberForever($key, function() use ($data) {
            $group =  PoiGroup::firstOrCreate([
                'title'   => $data['group'],
                'user_id' => $data['user_id']
            ]);

            return $group->id;
        });

        unset($data['group']);
    }

    public function getValidationBaseRules(): array
    {
        return [
            'name'            => 'required',
            'map_icon_id'     => 'required',
            'coordinates'     => 'required|array',
            'coordinates.lat' => 'lat',
            'coordinates.lng' => 'lng',
        ];
    }

    protected function getDefaults()
    {
        return $this->defaults;
    }
}
