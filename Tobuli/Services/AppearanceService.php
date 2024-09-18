<?php

namespace Tobuli\Services;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tobuli\Entities\User;
use Validator;

class AppearanceService
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var array
     */
    private $userWriteableSettings = [
        'server_name',
        'server_description',
        'default_language',
        'default_date_format',
        'default_time_format',
        'default_duration_format',
        'default_unit_of_distance',
        'default_unit_of_capacity',
        'default_unit_of_altitude',
        'map_center_latitude',
        'map_center_longitude',
        'map_zoom_level',

        'template_color',
        'login_page_background_color',
        'login_page_text_color',
        'login_page_panel_background_color',
        'login_page_panel_transparency',
        'welcome_text',
        'bottom_text',
        'apple_store_link',
        'google_play_link',

        'noreply_email',
        'from_name',
    ];

    /**
     * Saves server images and appearance setting
     *
     * @param array $requestData
     * @return bool
     */
    public function save($requestData): bool
    {
        $this->saveImages($requestData);

        return $this->saveSettings($requestData);
    }

    /**
     * Get specific appearance setting by key
     *
     * @param string $key
     * @return string|null
     */
    public function getSetting($key)
    {
        return Arr::get($this->getSettings(), $key);
    }

    /**
     * Get all settings by user
     *
     * @return array
     */
    public function getSettings()
    {
        if (is_null($this->settings)) {
            $this->loadSettings();
        }

        return $this->settings;
    }

    /**
     * Get url of selected asset
     *
     * @param string $type
     * @return string
     */
    public function getAssetFileUrl($type)
    {
        $file = $this->getAssetFilePath($type);

        $path = str_replace(public_path(), '', $file);

        if (app()->runningInConsole()){
            return \CustomFacades\Server::url().$path;
        }

        return asset_resource($path);
    }

    /**
     * Get absolute asset file path
     *
     * @param string $type
     * @return string|null
     */
    public function getAssetFilePath($type)
    {
        if (empty($this->user)) {
            return $this->getMainFilePath($type);
        }

        return $this->getUserFilePath($type)
            ?? $this->getMainFilePath($type);
    }

    /**
     * Check is asset file exists
     *
     * @param string $type
     * @return bool
     */
    public function assetFileExists($type)
    {
        return ! empty($this->getAssetFilePath($type));
    }

    /**
     * Checks if given path is valid image
     *
     * @param string $path Asset path
     * @return boolean
     */
    public function imageValid($path)
    {
        if (empty($path))
            return false;

        try {
            $file = new File($path);
        } catch (\Exception $e) {
            return false;
        }

        $validator = Validator::make(
            ['file' => $file],
            ['file' => 'file|mimetypes:image/x-icon,image/vnd.microsoft.icon,image/jpeg,image/png,image/gif,image/svg+xml']
        );

        return $validator->passes();
    }

    /**
     * Resolve which user's assets to use
     * @param User|null $user
     * @param boolean $force
     * @return self
     */
    public function resolveUser($user = null, $force = false)
    {
        if ($this->user && !$force)
            return $this;

        $this->user = null;

        if (is_null($user))
            $user = $this->getManager();

        if ($user && !$user->canChangeAppearance() && $user->manager_id)
            $user = User::getManagerTopFirst($user->manager_id);

        if (is_null($user)) {
            return $this;
        }

        $this->setUser($user);

        return $this;
    }

    /**
     * Set user who's assets to use
     * 
     * @param User $user
     * @return void
     */
    public function setUser(User $user)
    {
        $this->settings = null;

        $this->user = null;

        if ($user->isReseller()) {
            $this->user = $user;
        }
    }

    /**
     * Load appearance settings in to variable
     *
     * @return void
     */
    private function loadSettings()
    {
        $userSettings = empty($this->user) ? [] : $this->user->getSettings('appearance') ?? [];

        $defaultSettings = Arr::only(settings('main_settings'), $this->userWriteableSettings);

        $this->settings = array_merge($defaultSettings, $userSettings);
    }

    /**
     * Get manager based on session's referer id and current acting user
     *
     * @return User|null
     */
    private function getManager()
    {
        $user = getActingUser();

        if (!$user && $user_id = session()->get('referer_id', null))
            $user = User::getManagerTopFirst($user_id);

        if ($user && $user->canChangeAppearance())
            return $user;

        if ($user && $user->manager_id)
            return User::getManagerTopFirst($user->manager_id);

        return null;
    }

    /**
     * Return file path based on filename
     *
     * @param array $filename
     * @return string|null
     */
    private function getFilePath($filename)
    {
        $path = $filename['filename'] . '.' . ($filename['extension'] ?? '*');

        $file = current(glob($path));

        return empty($file)
            ? null
            : $file;
    }

    /**
     * Return main file path based on type
     *
     * @param string $type
     * @return string|null
     */
    private function getMainFilePath($type)
    {
        if (in_array($type, ['js', 'css']))
            return $this->getCustomMainFilePath($type);

        $filename = $this->getMainFilename($type);

        $path = $this->getFilePath($filename);

        if ($this->imageValid($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Return user's file path based on type
     *
     * @param string $type
     * @return string|null
     */
    private function getUserFilePath($type)
    {
        if (in_array($type, ['js', 'css']))
            return $this->getCustomUserFilePath($type);

        $filename = $this->getUserFilename($type);

        $path = $this->getFilePath($filename);

        if ($this->imageValid($path)) {
            return $path;
        }

        return null;
    }

    /**
     * Saves appearance settings
     *
     * @param array $requestData All request data
     * @return bool
     */
    private function saveSettings($requestData): bool
    {
        $settings = Arr::only($requestData, $this->userWriteableSettings);

        //reset settings cache
        $this->settings = null;

        if (empty($this->user)) {
            return settings('main_settings', array_merge(settings('main_settings'), $settings));
        }

        $userSettings = $this->user->getSettings('appearance');
        if (empty($userSettings))
            $userSettings = [];

        $this->user->setSettings('appearance', array_merge($userSettings, $settings));

        return true;
    }

    /**
     * Saves all new images
     *
     * @param array $requestData All request data
     * @return void
     */
    private function saveImages($requestData)
    {
        $filenames = $this->getFilenames();

        foreach ($filenames as $fileData) {
            $file = $requestData[$fileData['request_name']] ?? null;

            if (empty($file)) {
                continue;
            }

            $this->saveImage($fileData, $file);
        }
    }

    /**
     * Delete old image and save new one
     *
     * @param array $fileData File data
     * @param UploadedFile $file Uploaded file
     * @return void
     */
    private function saveImage($fileData, $file)
    {
        $path = $fileData['filename'];

        foreach (glob($path.'.*') as $filename) {
            unlink($filename);
        }

        $extension = $fileData['extension'] ?? strtolower($file->getClientOriginalExtension());
        $dir = dirname($path);
        $name = basename($path);

        $file->move($dir, "{$name}.{$extension}");
    }

    /**
     * Returns main assets path
     *
     * @return string
     */
    private function getMainAssetsPath()
    {
        return public_path('images/');
    }

    /**
     * Returns user's assets path
     *
     * @return string
     */
    private function getUserAssetsPath()
    {
        return public_path('images/logos/');
    }

    /**
     * Get all filenames based on user
     *
     * @return array
     */
    private function getFilenames()
    {
        return empty($this->user)
            ? $this->getMainFilenames()
            : $this->getUserFilenames();
    }

    /**
     * Get all main filenames
     *
     * @return array
     */
    private function getMainFilenames()
    {
        return $this->buildFilenames(null);
    }

    /**
     * Get main filename by type
     *
     * @param string $type
     * @return string|null
     */
    private function getMainFilename($type)
    {
        $result = $this->getMainFilenames();

        return $result[$type] ?? null;
    }

    /**
     * Get all user filenames
     *
     * @return array
     */
    private function getUserFilenames()
    {
        return $this->buildFilenames($this->user);
    }

    /**
     * Get user filename by type
     *
     * @param string $type
     * @return string|null
     */
    private function getUserFilename($type)
    {
        $result = $this->getUserFilenames();

        return $result[$type] ?? null;
    }

    /**
     * Returns available file names by user
     *
     * @param User|null $user
     * @return array
     */
    private function buildFilenames($user)
    {
        $suffix = is_null($user)
            ? ''
            : "-{$user->id}";

        $path = is_null($user)
            ? $this->getMainAssetsPath()
            : $this->getUserAssetsPath();

        return [
            'logo' => [
                'filename' => "{$path}logo{$suffix}",
                'request_name' => 'frontpage_logo',
            ],
            'logo-main' => [
                'filename' => "{$path}logo-main{$suffix}",
                'request_name' => 'login_page_logo',
            ],
            'background' => [
                'filename' => "{$path}background{$suffix}",
                'request_name' => 'background',
            ],
            'favicon' => [
                'filename' => "{$path}favicon{$suffix}",
                'request_name' => 'favicon',
                'extension' => 'ico',
            ],
        ];
    }

    private function getCustomMainFilePath($type)
    {
        return $this->getFilePath([
            'filename'  => public_path("assets/custom/$type"),
            'extension' => $type
        ]);
    }

    private function getCustomUserFilePath($type)
    {
        return $this->getFilePath([
            'filename'  => public_path("assets/custom/$type{$this->user->id}"),
            'extension' => $type
        ]);
    }
}
