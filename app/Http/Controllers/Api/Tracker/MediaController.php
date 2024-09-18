<?php

namespace App\Http\Controllers\Api\Tracker;

use Illuminate\Validation\Rule;
use Tobuli\Entities\MediaCategory;
use Tobuli\Entities\File\DeviceMedia;
use Tobuli\Exceptions\ValidationException;
use Tobuli\Services\FileService;
use Validator;

class MediaController extends ApiController
{
    public function uploadImage()
    {
        $data = request()->all();

        if (isBase64($data['file'] ?? '')) {
            $data['file'] = base64ToImage($data['file']);
        }

        $categories = MediaCategory::usersAccessible($this->deviceInstance->users)->pluck('id')->all();

        $validator = Validator::make($data, [
            'file' => 'required|image',
            'datetime' => 'required|date_format:Y-m-d H:i:s',
            'category' => Rule::in($categories),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator->messages());
        }

        $path = (new DeviceMedia())->getDirectory($this->deviceInstance);
        $timestamp = strtotime($data['datetime']);

        $fileService = new FileService();

        $category = isset($data['category']) ? $data['category'] . '.' : '';
        $filename = $fileService->generateFilename($path, $data['file']->getClientOriginalExtension());

        $filePath = $fileService->save($data['file'], $path, $category . $filename);
        $fileService->setFileTimestamps($filePath, $timestamp);

        return response()->json([
            'status' => 1,
        ]);
    }
}
