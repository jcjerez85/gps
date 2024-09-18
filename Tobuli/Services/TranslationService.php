<?php

namespace Tobuli\Services;

use Exception;
use App\Events\TranslationUpdated;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class TranslationService
{
    private $files;

    public function __construct()
    {
        $this->files = [
            'front',
            'admin',
            'global',
            'validation',
            'passwords',
            'reminders',
            'errors'
        ];
    }

    /**
     * Get translation file names
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Save translations
     *
     * @param  String $lang         Translated language
     * @param  array  $translations Translations array grouped by file
     * @throws Exception
     */
    public function save(string $lang, array $translations)
    {
        $trans = Arr::dot($translations);
        $this->writeStorageFile($lang, $trans);
        $this->writeTranslationFiles($lang, $trans);
    }

    /**
     * Update server's translation files
     * @throws Exception
     */
    public function updateTranslationFiles()
    {
        $this->copyTransFilesToOriginal();

        $langs = File::directories(storage_path('langs'));

        foreach ($langs as $lang) {
            $language = basename($lang);
            $files = File::files($lang);

            $translations = [];

            foreach ($files as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $trans = include($file);
                $translations = $this->mergeTranslations($translations, Arr::dot($trans, $fileName.'.'));
            }

            $this->writeTranslationFiles($language, $translations);
        }
    }

    /**
     * Get translations file content
     *
     * @param  String  $file     Specific translations file name
     * @param  String  $lang     Translations language
     * @param  Bool    $original Wheter to use original translations folder or standart
     * @return array
     */
    public function getTranslations(string $file, string $lang, $original = false)
    {
        $translations = [];

        $files = $this->files;

        if (isset($file)) {
            $files = array_intersect($files, [$file]);
        }

        foreach ($files as $currFile) {
            if ($original) {
                $path = $this->getOriginalBasePath($currFile, $lang);
            } else {
                $path = $this->getBasePath($currFile, $lang);
            }

            $translations[$currFile] = include($path);
        }

        $translations = Arr::dot($translations);

        return $translations;
    }

    /**
     * Get translation file content
     *
     * @param String $key Specific translations key
     * @param String $lang Translations language
     * @param Bool $original Whether to use original translations folder or standart
     * @return String
     */
    public function getTranslation(string $key, string $lang, $original = false)
    {
        list($file, $transKey) = explode('.', $key, 2);

        $translations = $this->getTranslations($file, $lang, $original);

        return Arr::get($translations, $transKey);
    }

    /**
     * Get translation file text with new translations
     *
     * @param string $lang
     * @param array $data
     * @param Bool $useStorage Whether to use translations from storage
     * @throws Exception
     * @return array
     */
    private function parseTranslations(string $lang, array $data, $useStorage = false)
    {
        $result = [];

        $originalTranslations = $useStorage
            ? $this->getStorageTranslations($lang, $data)
            : $this->getOriginalTranslations($lang, $data);
        $arr = $this->mergeTranslations($originalTranslations, $data);
        $translations = array_undot($arr);

        foreach ($translations as $file => $newTranslations) {
            $result[$file] = $this->formatFileContent($newTranslations);
        }

        return $result;
    }

    /**
     * Merge two translation arrays
     *
     * @param  array  $translations    Previous translations
     * @param  array  $newTranslations New translations
     * @return array
     */
    private function mergeTranslations(array $translations, array $newTranslations)
    {
        return array_replace_recursive($translations, $newTranslations);
    }

    /**
     * Wraps data in php tag
     *
     * @param  array $data Data to be inserted
     * @return String
     */
    private function formatFileContent(array $data)
    {
        return "<?php\nreturn ".exportVar($data, '<br>').";\n";
    }

    /**
     * Finds placeholders in string
     *
     * @param  String $string
     * @return array
     */
    private function getPlaceholdersInString(string $string)
    {
        preg_match_all('/:\w+/', $string, $matches);

        return empty($matches[0]) ? [] : $matches[0];
    }

    /**
     * Get existing placeholders in specific translation
     *
     * @param  String  $key  Translation key
     * @return array
     */
    public function getPlaceholders(string $key)
    {
        $result = [];

        $translation = $this->getTranslation($key, 'en', true);

        if ($translation) {
            $result = $this->getPlaceholdersInString($translation);
        }

        return $result;
    }

    /**
     * Write new data to specified translation file
     *
     * @param  String  $lang Translations language
     * @param  array   $data New content to be put in translations file
     * @throws Exception
     */
    private function writeTranslationFiles(string $lang, array $data)
    {
        $parsedTranslations = $this->parseTranslations($lang, $data);

        foreach ($parsedTranslations as $file => $translations) {
            $filePath = $this->getBasePath($file, $lang);
            @chmod($filePath, 0777);

            if (File::put($filePath, $translations)) {
                continue;
            }

            throw new Exception(trans('global.cant_write_to_file', ['file' => trans('admin.translations')]));
        }
    }

    /**
     * Write new data to specified translation file in storage folder
     *
     * @param  String  $lang Translations language
     * @param  array  $data New content to be put in translations file
     * @throws Exception
     */
    private function writeStorageFile(string $lang, array $data)
    {
        $parsedTranslations = $this->parseTranslations($lang, $data, true);

        foreach ($parsedTranslations as $file => $translations) {
            $filePath = $this->getStoragePath($lang, $file);

            if (!file_put_contents($filePath, $translations)) {
                throw new Exception(trans('global.cant_write_to_file', ['file' => trans('admin.storage')]));
            }

            event(new TranslationUpdated($file, $translations));
        }
    }

    /**
     * Get path of storage translations file
     *
     * @param  String  $lang Translations language
     * @param  String  $file Specific translations file name
     * @throws Exception
     * @return String
     */
    private function getStoragePath(string $lang, string $file)
    {
        $dir = storage_path("langs/{$lang}");

        if (!File::isDirectory($dir)) {
            if (!File::makeDirectory($dir, 0777, true)) {
                throw new Exception(trans('global.cant_create_path', ['path' => trans('admin.storage')]));
            }
        }

        return "{$dir}/{$file}.php";
    }

    /**
     * Get base path of translations location
     *
     * @param  String|null  $file Specific translations file name. Cannot be used without language param
     * @param  String|null  $lang Translations language
     * @return String
     */
    private function getBasePath(string $file = null, string $lang = null)
    {
        return base_path('resources/lang'.$this->formatLanguageAndFile($file, $lang));
    }

    /**
     * Get base path of original translations location
     *
     * @param  String|null  $file Specific translations file name. Cannot be used without language param
     * @param  String|null  $lang Translations language
     * @return String
     */
    private function getOriginalBasePath(string $file = null, string $lang = null)
    {
        return base_path('resources/original_lang'.$this->formatLanguageAndFile($file, $lang));
    }

    /**
     * Returns language and file part of path
     *
     * @param  String|null  $file Specific translations file name. Cannot be used without language param
     * @param  String|null  $lang Translations language
     * @return String
     */
    private function formatLanguageAndFile(string $file = null, string $lang = null)
    {
        $result = [];

        if ($lang) {
            $result['lang'] = '/'.$lang;

            if ($file) {
                $result['file'] = '/'.$file.(!pathinfo($file, PATHINFO_EXTENSION) ? '.php' : '');
            }
        }

        return implode('', $result);
    }

    /**
     * Get storage translations
     *
     * @param  String $lang Translations language
     * @param  array  $trans Array of translation key and value pairs
     * @throws Exception
     * @return array
     */
    private function getStorageTranslations(string $lang, array $trans)
    {
        $storageTranslations = [];
        $translations = array_undot($trans);

        foreach ($translations as $file => $transKeys) {
            $filePath = $this->getStoragePath($lang, $file);

            if (is_file($filePath)) {
                $fileTranslations = Arr::dot(include($filePath), $file.'.');
                $storageTranslations = $this->mergeTranslations($storageTranslations, $fileTranslations);
            }
        }

        return $storageTranslations;
    }

    /**
     * Get original translations
     *
     * @param  String $lang Translations language
     * @param  array  $trans Array of translation key and value pairs
     * @return array
     */
    private function getOriginalTranslations(string $lang, array $trans)
    {
        $originalTranslations = [];
        $translations = array_undot($trans);

        foreach ($translations as $file => $transKeys) {
            if (!is_file($this->getBasePath($file, $lang))) {
                if (!is_file($this->getOriginalBasePath($file, $lang))) {
                    $fileTranslations = $this->getTranslations($file, 'en', true);
                } else {
                    $fileTranslations = $this->getTranslations($file, $lang, true);
                }
            } else {
                $fileTranslations = $this->getTranslations($file, $lang);
            }

            $originalTranslations = $this->mergeTranslations($originalTranslations, $fileTranslations);
        }

        return $originalTranslations;
    }

    /**
     * Copies current translations to original destination
     *
     */
    private function copyTransFilesToOriginal()
    {
        $current = $this->getBasePath();
        $original = $this->getOriginalBasePath();

        if (!File::exists($original)) {
            File::makeDirectory($original, $mode = 0777, true, true);
        }

        File::copyDirectory($current, $original);
    }
}
