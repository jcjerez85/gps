<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SmsGateway extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if( ! Schema::hasColumn('users', 'sms_gateway')) {
            Schema::table('users', function($table)
            {
                $table->boolean('sms_gateway')->index()->default(0);
                $table->string('sms_gateway_url', 500)->nullable();
            });
        }

        if( ! Schema::hasColumn('alerts', 'mobile_phone')) {
            DB::statement('ALTER TABLE alerts MODIFY COLUMN email TEXT');

            Schema::table('alerts', function ($table) {
                $table->text('mobile_phone')->after('email')->nullable();
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
        Schema::table('users', function($table)
        {
            $table->dropColumn('sms_gateway');
            $table->dropColumn('sms_gateway_url');
        });

        Schema::table('alerts', function($table)
        {
            $table->dropColumn('mobile_phone');
        });
    }

}
