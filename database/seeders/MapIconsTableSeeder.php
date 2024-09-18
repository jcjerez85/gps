<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\File;
use Tobuli\Repositories\MapIcon\MapIconRepositoryInterface as MapIcon;
use Illuminate\Database\Seeder;

class MapIconsTableSeeder extends Seeder {
    /**
     * @var MapIcon
     */
    private $mapIcon;

    public function __construct(MapIcon $mapIcon)
    {
        $this->mapIcon = $mapIcon;
    }

	public function run()
    {
        # Icons
        $folder = base_path('images/map_icons');

        if( ! File::isDirectory($folder))
            $folder = public_path('images/map_icons');

        $files = File::allFiles($folder);

        foreach ($files as $file) {
            if (!is_object($file) || empty($file->getFilename()))
                continue;

            list($width, $height) = getimagesize($file);
            if (!$width || !$height)
                continue;

            $path = 'images/map_icons/' . $file->getFilename();

            if ($this->mapIcon->findWhere(['path' => $path]))
                continue;

            $this->mapIcon->create([
                'path'   => $path,
                'width'  => $width,
                'height' => $height,
            ]);
        }
    }
}