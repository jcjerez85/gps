<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStDistanceSphere2dFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ($this->functionExists('ST_DISTANCE_SPHERE_2D')) {
            return;
        }

        DB::unprepared("
        CREATE FUNCTION `ST_DISTANCE_SPHERE_2D`(`lat1` DOUBLE, `lng1` DOUBLE, `lat2` DOUBLE, `lng2` DOUBLE) RETURNS DOUBLE
            BEGIN
            
            DECLARE distance DOUBLE;
            
            SET distance = (SELECT (6371 * ACOS( 
                                                 COS(RADIANS(lat2)) 
                                               * COS(RADIANS(lat1)) 
                                               * COS(RADIANS(lng1) - RADIANS(lng2))       
                                               + SIN(RADIANS(lat2)) 
                                               * SIN(RADIANS(lat1))
                            )) AS distance); 
            
            IF (distance IS NULL) THEN
                RETURN -1;
            ELSE 
                RETURN distance * 1000;
            END IF;
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!$this->functionExists('ST_DISTANCE_SPHERE_2D')) {
            return;
        }

        DB::statement('DROP FUNCTION ST_DISTANCE_SPHERE_2D');
    }

    private function functionExists(string $function): bool
    {
        return DB::query()
            ->from('information_schema.routines')
            ->where('routine_schema', DB::connection()->getDatabaseName())
            ->where('routine_name', $function)
            ->count();
    }
}
