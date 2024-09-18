<?php

namespace App\Http\Controllers\Frontend;

use App\Exceptions\ResourseNotFoundException;
use App\Http\Controllers\Controller;
use App\Jobs\VideoConvert;
use App\Transformers\ApiV1\DeviceCameraMediaTransformer;
use CustomFacades\Repositories\UserRepo;
use Formatter;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Tobuli\Entities\DeviceCamera;
use Tobuli\Entities\File\DeviceCameraMedia;
use Tobuli\Entities\File\DeviceMedia;
use Tobuli\Entities\File\FileSorter;
use Tobuli\Entities\MediaCategory;
use Tobuli\Exceptions\ValidationException;
use ZipArchive;

class DeviceMediaController extends Controller
{
    /**
     * Create view of media.
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $deviceCollection = $this->user->devices()
            ->search(Request::input('search_phrase'))
            ->orderBy('name', 'asc')
            ->paginate(15);

        $deviceId = Request::input('device_id');

        return view('front::DeviceMedia.create')->with(compact('deviceCollection', 'deviceId'));
    }

    public function getImages($deviceId)
    {
        $this->checkException('camera', 'view');

        $categoriesEnabled = $this->user->perm('media_categories', 'view');

        /** @var LengthAwarePaginator $images */
        list($images, $sort) = $this->getDeviceImages($deviceId, $categoriesEnabled, request('filter') ?: [], request('sorting'));

        if ($this->api) {
            return response()->json(array_merge(
                ['success' => true],
                \FractalTransformer::paginate($images, DeviceCameraMediaTransformer::class)->toArray()
            ));
        }

        $categories = $categoriesEnabled
            ? MediaCategory::userAccessible($this->user)->pluck('title', 'id')
            : new Collection();

        return view('front::DeviceMedia.images', [
            'images' => $images,
            'sort' => $sort,
            'deviceId' => $deviceId,
            'mediaCategories'   => $categories,
            'categoriesEnabled' => $categoriesEnabled && count($categories),
        ]);
    }

    public function getImagesTable($deviceId)
    {
        $this->checkException('camera', 'view');

        $categoriesEnabled = $this->user->perm('media_categories', 'view');

        list($images, $sort) = $this->getDeviceImages($deviceId, $categoriesEnabled, request('filter') ?: [], request('sorting'));

        return view('front::DeviceMedia.images_table', [
            'images' => $images,
            'sort' => $sort,
            'deviceId' => $deviceId,
            'categoriesEnabled' => $categoriesEnabled,
        ]);
    }

    private function getDeviceImages(int $deviceId, bool $categoriesEnabled, array $filters, $sort)
    {
        $sort = array_merge(['sort_by' => 'date_modified', 'sort' => 'desc'], (array)$sort);
        $limit = (int)request('limit', 15);

        $device = UserRepo::getDevice($this->user->id, $deviceId);
        $this->checkException('devices', 'show', $device);

        try {
            DeviceCameraMedia::setCategoriesEnabled($categoriesEnabled);
            $fileQuery = DeviceCameraMedia::setEntity($device);

            /** @var FileSorter $fileSorter */
            $fileSorter = $fileQuery->getEntityFileSorter()->sortBy($sort['sort_by'], $sort['sort']);

            if (!empty($filters['date_from'])) {
                $fileSorter->from(Formatter::time()->reverse($filters['date_from']));
            }

            if (!empty($filters['date_to'])) {
                $fileSorter->to(Formatter::time()->reverse($filters['date_to']));
            }

            if ($categoriesEnabled && !empty($filters['media_category'])) {
                $fileSorter->nameStartsWith($filters['media_category'] . '.');
            }

            $images = $fileQuery->paginate($limit, $fileSorter)->appends(request()->all());
        } catch (ResourseNotFoundException $e) {
            $images = collect();
        }

        return [$images, $sort];
    }

    public function getImage($device_id, $filename)
    {
        $device = UserRepo::getDevice($this->user->id, $device_id);

        $this->checkException('devices', 'show', $device);
        $this->checkException('camera', 'view');

        $image = DeviceMedia::setEntity($device)->find($filename);

        if ( ! $image) {
            throw new ResourseNotFoundException('front.image');
        }

        $item = $this->objectForMapDisplay($device, $image);

        if ($this->api)
            return response()->json(['success' => true, 'item' => $item, 'image' => $image->toArray()]);

        if ($image->isConvertable())
            $this->dispatch(new VideoConvert($image->path, $device, $this->user));

        return view('front::DeviceMedia.image', [
            'image' => $image,
            'item' => $item,
            'camera_id' => 0,
        ]);
    }

    public function getFile($device_id, $filename)
    {
        $device = UserRepo::getDevice($this->user->id, $device_id);

        $this->checkException('devices', 'show', $device);
        $this->checkException('camera', 'view');

        $image = DeviceMedia::setEntity($device)->find($filename);

        if ( ! $image) {
            throw new ResourseNotFoundException('front.image');
        }

        $path = $image->path;

        if ($image->isImage()) {
            $file = File::get($path);
            $type = File::mimeType($path);

            return response($file, 200)->header("Content-Type", $type);
        }

        return response()->download($path);
    }

    public function getCameraFile($camera_id, $filename)
    {
        $camera = DeviceCamera::find($camera_id);

        $this->checkException('camera', 'show', $camera);

        $image = DeviceCameraMedia::setEntity($camera)->find($filename);

        if ( ! $image) {
            throw new ResourseNotFoundException('front.image');
        }

        $path = $image->path;

        if ($image->isImage()) {
            $file = File::get($path);
            $type = File::mimeType($path);

            return response($file, 200)->header("Content-Type", $type);
        }

        return response()->download($path);
    }

    public function remove($device_id, $filename)
    {
        return $this->deleteImage($device_id, [$filename]);
    }

    public function removeMulti($device_id)
    {
        return $this->deleteImage($device_id, request()->get('id') ?: []);
    }

    private function deleteImage($deviceId, array $filenames)
    {
        $device = UserRepo::getDevice($this->user->id, $deviceId);

        $this->checkException('devices', 'show', $device);
        $this->checkException('camera', 'remove');

        $count = 0;
        $undeleted = [];
        $deviceMedia = DeviceMedia::setEntity($device);

        foreach ($filenames as $filename) {
            $image = $deviceMedia->find($filename);

            if (!$image) {
                continue;
            }

            if ($image->delete()) {
                $count++;
            } else {
                $undeleted[] = $filename;
            }
        }

        $this->waitDeleting($deviceMedia, $undeleted, $count);

        return response()->json([
            'status' => 1,
            'delete_count' => $count
        ]);
    }

    private function waitDeleting(DeviceMedia $deviceMedia, array $undeletedFiles, int &$count)
    {
        $i = 10;

        while ($undeletedFiles && $i--) {
            sleep(1);

            $undeletedFiles = array_filter($undeletedFiles, function($filename) use ($deviceMedia, &$count) {
                if ($deviceMedia->find($filename))
                    return true;

                $count++;

                return false;
            });
        }
    }

    public function downloadMulti($device_id)
    {
        $device = UserRepo::getDevice($this->user->id, $device_id);
        $filenames = request()->get('id');

        $this->checkException('devices', 'show', $device);
        $this->checkException('camera', 'view');

        $zip = new ZipArchive();
        $tmpFile = storage_path('cache/images_' . time() . '.zip');

        if ($zip->open($tmpFile, ZipArchive::CREATE) !== true) {
            throw new ValidationException('Unable to open archive');
        }

        foreach ($filenames as $filename) {
            if ($image = DeviceMedia::setEntity($device)->find($filename)) {
                $zip->addFile($image->path, basename($image->path));
            }
        }

        $zip->close();

        return response()->download($tmpFile)->deleteFileAfterSend(true);
    }

    public function download($device_id, $filename)
    {
        $image = null;
        $device = UserRepo::getDevice($this->user->id, $device_id);

        $this->checkException('devices', 'show', $device);
        $this->checkException('camera', 'view');

        $image = DeviceMedia::setEntity($device)->find($filename);

        if ( ! $image) {
            throw new ResourseNotFoundException('front.image');
        }

        return response()->download($image->path);
    }

    private function objectForMapDisplay($device, $image)
    {
        if (isset($image)) {
            $closest_position = $device->positions()
                ->where('valid', true)
                ->whereNotNull('time')
                ->orderByRaw("abs(TIMESTAMPDIFF(second, time, '" . $image->created_at . "')) ASC")
                ->first();
        }

        $tail_coords = [];

        if (isset($closest_position)) {
            $tail_collection = $device->positions()
                ->where('id', '<', $closest_position->id)
                ->where('distance', '>', 0.02)
                ->where('valid', true)
                ->whereNotNull('time')
                ->orderBy('time', 'DESC')
                ->orderBy('id', 'DESC')
                ->take(10)
                ->get();

            foreach ($tail_collection as $tail) {
                $tail_coords[] = ['lat' => (string)$tail->latitude, 'lng' => (string)$tail->longitude];
            }
        }

        $item = new \stdClass();
        $item->org_id = $device->id;
        $item->id = null;
        $item->tail = $tail_coords;
        $item->tail_color = $device->tail_color;
        $item->name = $device->name;
        $item->speed = $device->getSpeed($closest_position) ?? '';
        $item->course = $closest_position->course ?? '';
        $item->lat = (string)($closest_position->latitude ?? '');
        $item->lng = (string)($closest_position->longitude ?? '');
        $item->altitude = $closest_position->altitude ?? '';
        $item->time = $closest_position ? Formatter::time()->human($closest_position->time) : '';
        $item->timestamp = $closest_position ? strtotime($closest_position->time) : null;
        $item->online = $device->getStatus();

        return $item;
    }
}
