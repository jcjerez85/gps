<?php

namespace Tobuli\Cache;


use Illuminate\Cache\DatabaseStore;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SqliteStore extends DatabaseStore
{

    public function __construct()
    {
        $config = config('cache.stores.sqlite');

        parent::__construct(
            app('db')->connection('sqlite'),
            $config['table'],
            $config['prefix']
        );
    }

    public function flush()
    {
        if (Schema::connection('sqlite')->hasTable('cache')) {
            DB::connection('sqlite')->table('cache')->delete();
            //optimizes db, including filesize
            DB::connection('sqlite')->statement('VACUUM;');
        }

        return true;
    }
}