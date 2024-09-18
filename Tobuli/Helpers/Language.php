<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\App;

class Language
{
    protected $data;
    protected $langKey = '';

    public function __construct($language)
    {
        $this->set($language);
    }

    public function set($language)
    {
        try {
            $this->data = $this->getLanguage($language);
        } catch (\Exception $e) {
            try {
                $this->data = $this->getLanguageByProps($language);
            } catch (\Exception $e) {
                $language = 'en';
                $this->data = $this->getLanguage($language);
            }
        }
        
        App::setLocale($language);

        listsTranslations();
    }

    public function get() {
        return $this->data;
    }

    public function key() {
        return $this->key;
    }

    public function iso() {
        return $this->iso;
    }

    public function dir() {
        return $this->dir;
    }

    public function flag() {
        if (empty($this->data['flag']))
            return asset("assets/images/header/en.png");

        return asset("assets/images/header/{$this->data['flag']}");
    }

    private function getLanguage($language)
    {
        $data = settings("languages.$language");

        if (empty($data))
            throw new \Exception('Language "'.$language.'" not fount');

        return $data;
    }

    private function getLanguageByProps($language, array $props = ['key', 'iso', 'locale'])
    {
         foreach (settings('languages') as $setting) {
             foreach ($props as $prop) {
                 if ($setting[$prop] === $language) {
                     return $setting;
                 }
             }
         }

        throw new \Exception('Language "'.$language.'" not fount');
    }

    public function __get($key) {
        if ( ! array_key_exists($key, $this->data))
            return null;

        return $this->data[$key];
    }

    public function setLangKey(string $langKey): self
    {
        $this->langKey = $langKey;

        return $this;
    }

    public function getLangKey(): string
    {
        return $this->langKey;
    }
}