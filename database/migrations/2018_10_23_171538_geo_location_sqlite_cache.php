<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

class GeoLocationSqliteCache extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $database = config('database.connections.sqlite.database');

        if ( ! File::exists($database)) {
            File::put($database, '');
            exec("chmod -R 0777 $database");
        }

        if (Schema::connection('sqlite')->hasTable('cache')) { return; }

        Schema::connection('sqlite')->create('cache', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->text('value')->nullable(false);
            $table->integer('expiration')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sqlite')->drop('cache');
    }
}
