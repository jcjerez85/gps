<?php namespace App\Providers;

use CustomFacades\Repositories\DeviceRepo;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator as IlluminateValidator;
use Tobuli\Helpers\CssPredefinedColorsUtil;
use Tobuli\Helpers\ParsedCurl;
use Validator;

class ValidatorRulesServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Validator::extend('same_protocol', function ($attribute, $value, $parameters, $validator) {

            $protocols = DeviceRepo::getProtocols($value)->pluck('protocol', 'protocol')->all();

            if (count($protocols) > 1)
                return false;

            return true;
        });

        Validator::extend('contains', function ($attribute, $value, $parameters, $validator) {
            if (!count($parameters) || strpos($value, $parameters[0]) === false)
                return false;

            return true;
        });

        Validator::replacer('contains', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':value', $parameters[0], $message);
        });

        Validator::extend('not_contains', function ($attribute, $value, $parameters, $validator) {

            if ( ! count($parameters))
                return false;

            if (strpos($value, $parameters[0]) !== false)
                return false;

            return true;
        });

        Validator::replacer('not_contains', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':value', $parameters[0], $message);
        });

        Validator::extend('key_value_format', function ($attribute, $value, $parameters, $validator) {
            $headers_array = array_filter(explode(';', $value));
            $headers_array = array_map('trim',  $headers_array);

            $pattern = '/^(^.*:.*;?)+$/';
            foreach ($headers_array as $header) {
                if (! preg_match($pattern, $header))
                    return false;
            }

            return true;
        });

        Validator::extend('ip_port', function ($attribute, $value, $parameters, $validator) {

            $parts = explode(':', $value);

            if (count($parts) !== 2)
                return false;

            if (filter_var($parts[0], FILTER_VALIDATE_IP) === false)
                return false;

            if (filter_var($parts[1], FILTER_VALIDATE_INT) === false)
                return false;

            return true;
        });

        Validator::extend('host', function ($attribute, $value, $parameters, $validator) {
            return (bool)filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
        });

        Validator::extend('host_port', function ($attribute, $value, $parameters, $validator) {
            if (!is_scalar($value)) {
                return false;
            }

            $parts = explode(':', $value);

            if (count($parts) !== 2) {
                return false;
            }

            return filter_var($parts[1], FILTER_VALIDATE_INT) !== false
                && filter_var($parts[0], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME);
        });

        Validator::extend('semicolon_array', function ($attribute, $value, $parameters, IlluminateValidator $validator) {
            return $this->semicolCheck($attribute, $value, $parameters, $validator, '');
        });

        Validator::extend('semicolon_element', function ($attribute, $value, $parameters, IlluminateValidator $validator) {
            return $this->semicolCheck($attribute, $value, $parameters, $validator, '.*');
        });

        Validator::extend('lat', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value <= 90 && $value >= -90;
        });

        Validator::extend('lng', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value <= 180 && $value >= -180;
        });

        Validator::extend('is_language', function($attribute, $value, $parameters, $validator) {
            $language = settings('languages.'.$value);

            return !empty($language);
        });

        Validator::extend('placeholder', function($attribute, $value, $parameters, $validator) {
            if (!count($parameters) || strpos($value, $parameters[0]) === false)
                return false;

            return true;
        });

        Validator::replacer('placeholder', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':placeholder', $parameters[0], $message);
        });

        Validator::extend('translation_file', function($attribute, $value, $parameters, $validator) {
            $translationService = new \Tobuli\Services\TranslationService();
            $files = $translationService->getFiles();
            $files = array_merge($files, ['all']);

            return in_array($value, $files);
        });

        Validator::extend('image_valid', function ($attribute, $value, $parameters, $validator) {
            return getimagesize($value) !== false;
        });

        Validator::extend('contains_uppercase', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[A-Z]/', $value);
        });

        Validator::extend('contains_lowercase', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[a-z]/', $value);
        });

        Validator::extend('contains_digit', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/[0-9]/', $value);
        });

        Validator::extend('strong_password', function ($attribute, $value, $parameters, $validator) {
            $validator->addReplacer('strong_password', function($message, $attribute, $rule, $parameters) use ($value) {
                $failedRules = [];

                if (mb_strlen($value) < 8) {
                    $failedRules[] = strtolower(trans('validation.min.string', ['min' => 8]));
                }

                if (!preg_match('/(?=.*\d)/', $value)) {
                    $failedRules[] = strtolower(trans('validation.digit_character'));
                }

                if (!preg_match('/(?=.*[A-Z])/', $value)) {
                    $failedRules[] = strtolower(trans('validation.uppercase_character'));
                }

                if (!preg_match('/(?=.*[a-z])/', $value)) {
                    $failedRules[] = strtolower(trans('validation.lowercase_character'));
                }

                return $message.implode(', ', $failedRules);
            });

            return mb_strlen($value) >= 8 && preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/', $value);
        });

        Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match("/^\+\d[0-9]{10}/", $value);
        });

        Validator::extend('array_max', function ($attribute, $value, $parameters, $validator) {
            return count($value) <= $parameters['0'];
        });

        Validator::replacer('array_max', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max', $parameters[0], $message);
        });

        Validator::extend('lesser_than', function ($attribute, $value, $parameters, $validator) {
            return $value < Arr::get($validator->getData(), $parameters[0]);
        });

        Validator::replacer('lesser_than', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':other', trans('validation.attributes.'.$parameters[0]), $message);
        });

        Validator::extend('css_color', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}|(?:rgba?|hsla?)\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i', $value)
                || in_array($value, CssPredefinedColorsUtil::LIST);
        });

        Validator::extend('curl_request', function ($attribute, $value) {
            return (new ParsedCurl($value))->isValid();
        });

        Validator::replacer('curl_request', function () {
             return implode(' | ', ParsedCurl::getLastErrors());
        });

        Validator::extend('unique_table_msg', function ($attribute, $value, $parameters, $validator) {
            $query = \DB::query()
                ->from($parameters[0])
                ->where($parameters[1], $value);

            if (isset($parameters[2]) && $parameters[2] === '%s') {
                $data = $validator->getData();

                $id = $data['id'] ?? null;
                $column = $parameters[3] ?? 'id';

                $query->where($column, '!=', $id);
            }

            return $query->count() === 0;
        });

        Validator::replacer('unique_table_msg', function ($message, $attribute, $rule, $parameters) {
            return trans('validation.unique_table_msg', [
                'attribute' => $parameters[1],
                'table' => $parameters[0],
            ]);
        });

        Validator::extendImplicit('required_if_in_array', function ($attribute, $value, $parameters, $validator) {
            if (strlen(trim($value)) > 0)
                return true;

            // The first item in the array of parameters is the field that we take the value from
            $valueField = array_shift($parameters);

            $valueFieldValues = Arr::get($validator->getData(), $valueField);

            if (is_null($valueFieldValues)) {
                return true;
            }

            if (!is_array($valueFieldValues)) {
                $valueFieldValues = [$valueFieldValues];
            }

            foreach ($parameters as $parameter) {
                if (in_array($parameter, $valueFieldValues)) {
                    return false;
                }
            }

            return true;
        });

        Validator::replacer('required_if_in_array', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', trans('validation.attributes.'.$attribute), trans('validation.required'));
        });
    }

    public function register()
    {
    }

    private function semicolCheck($attribute, $value, $parameters, IlluminateValidator $validator, string $dataPostfix): bool
    {
        $rules = explode(';', implode(',', $parameters));
        $value = explode(';', $value);

        foreach (array_reverse(explode('.', $attribute)) as $part) {
            $value = [$part => $value];
        }

        $tmpValidator = Validator::make($value, [$attribute . $dataPostfix => $rules]);

        if ($tmpValidator->fails()) {
            $validator->errors()->merge($tmpValidator->errors());
        }

        return true;
    }
}