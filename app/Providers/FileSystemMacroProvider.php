<?php

namespace App\Providers;


use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Tobuli\Exceptions\ValidationException;

class FileSystemMacroProvider extends ServiceProvider
{
    public function boot()
    {
        /*
         * Fetch files in order by date.
         */
        File::macro('orderByDate', function ($files, $order) {
            switch ($order) {
                case 'desc':
                    $operator = '<';
                    break;
                case 'asc':
                    $operator = '>';
                    break;
                default:
                    throw new ValidationException(trans('validation.attributes.bad_order_operator'));
            }

            usort($files, function ($a, $b) use ($operator) {
                return version_compare(File::lastModified($a), File::lastModified($b), $operator);
            });

            return $files;
        });
    }

    public function register()
    {
        //
    }
}