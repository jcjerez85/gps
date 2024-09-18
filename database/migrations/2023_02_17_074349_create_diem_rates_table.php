<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiemRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('diem_rates')) {
            Schema::create('diem_rates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->boolean('active');
                $table->float('amount');
                $table->unsignedSmallInteger('period');
                $table->string('period_unit', 1);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('geofences', 'diem_rate_id')) {
            Schema::table('geofences', function (Blueprint $table) {
                $table->integer('diem_rate_id')->unsigned()->nullable()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diem_rates');

        if (Schema::hasColumn('geofences', 'diem_rate_id')) {
            Schema::table('geofences', function (Blueprint $table) {
                $table->dropColumn('diem_rate_id');
            });
        }
    }
}
