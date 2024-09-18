<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('custom_fields', 'description')) {
            return;
        }

        Schema::table('custom_fields', function ($table) {
            $table->string('description')
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('custom_fields', 'description')) {
            return;
        }

        Schema::table('custom_fields', function($table) {
            $table->dropColumn('description');
        });
    }
}
