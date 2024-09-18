<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommandTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createMain();
        $this->createDeviceRelations();
        $this->createDeviceTypeRelations();

        Schema::dropIfExists('user_gprs_template_device_types');
        Schema::dropIfExists('user_gprs_template_devices');
        Schema::dropIfExists('user_gprs_templates');
        Schema::dropIfExists('user_sms_templates');
    }

    private function createMain()
    {
        if (!Schema::hasTable('command_templates')) {
            Schema::create('command_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->string('type', 32)->nullable()->index();
                $table->integer('user_id')->unsigned()->index()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('adapted', 64)->nullable()->default(null)->index();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->string('protocol', 20)->nullable();
                $table->timestamps();
            });
        }

        DB::statement("
         INSERT IGNORE INTO command_templates
                     SELECT id, 'gprs', user_id, adapted, title, message, protocol, created_at, updated_at
                       FROM user_gprs_templates g
                      WHERE EXISTS(SELECT 1 FROM users u WHERE u.id = g.user_id)");

        DB::statement("
            INSERT INTO command_templates
                 SELECT NULL, 'sms', user_id, NULL, title, message, NULL, created_at, updated_at
                   FROM user_sms_templates s
                  WHERE EXISTS(SELECT 1 FROM users u WHERE u.id = s.user_id)");

        DB::statement("
            DELETE FROM command_templates
                  WHERE id NOT IN (SELECT id FROM (  SELECT MIN(id) AS id
                                                       FROM command_templates
                                                   GROUP BY type, user_id, adapted, title, message, protocol, created_at, updated_at) as tmp)");
    }

    private function createDeviceRelations()
    {
        if (!Schema::hasTable('command_template_devices')) {
            Schema::create('command_template_devices', function (Blueprint $table) {
                $table->integer('device_id')->unsigned()->index();
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
                $table->integer('command_template_id')->unsigned()->index();
                $table->foreign('command_template_id')->references('id')->on('command_templates')->onDelete('cascade');
                $table->primary(['device_id', 'command_template_id'], 'command_template_devices_pk');
            });
        }

        DB::statement("INSERT IGNORE INTO command_template_devices SELECT * FROM user_gprs_template_devices");
    }

    private function createDeviceTypeRelations()
    {
        if (!Schema::hasTable('command_template_device_types')) {
            Schema::create('command_template_device_types', function (Blueprint $table) {
                $table->integer('device_type_id')->unsigned()->index();
                $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
                $table->integer('command_template_id')->unsigned()->index();
                $table->foreign('command_template_id')->references('id')->on('command_templates')->onDelete('cascade');
                $table->primary(['device_type_id', 'command_template_id'], 'command_template_device_types_pk');
            });
        }

        DB::statement("INSERT IGNORE INTO command_template_device_types SELECT * FROM user_gprs_template_device_types");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->revertMain();
        $this->revertDeviceRelations();
        $this->revertDeviceTypeRelations();

        Schema::dropIfExists('command_template_device_types');
        Schema::dropIfExists('command_template_devices');
        Schema::dropIfExists('command_templates');
    }

    private function revertMain()
    {
        if (!Schema::hasTable('user_gprs_templates')) {
            Schema::create('user_gprs_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('adapted', 64)->nullable()->default(null)->index();
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->string('protocol', 20)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_sms_templates')) {
            Schema::create('user_sms_templates', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('title')->nullable();
                $table->text('message')->nullable();
                $table->timestamps();
            });
        }

        DB::statement("
         INSERT IGNORE INTO user_gprs_templates
                     SELECT id, user_id, adapted, title, message, protocol, created_at, updated_at
                       FROM command_templates
                      WHERE type = 'gprs' AND user_id IS NOT NULL");

        DB::statement("
         INSERT IGNORE INTO user_sms_templates
                     SELECT id, user_id, title, message, created_at, updated_at
                       FROM command_templates
                      WHERE type = 'sms' AND user_id IS NOT NULL");
    }

    private function revertDeviceRelations()
    {
        if (!Schema::hasTable('user_gprs_template_devices')) {
            Schema::create('user_gprs_template_devices', function (Blueprint $table) {
                $table->integer('device_id')->unsigned()->index();
                $table->foreign('device_id')->references('id')->on('devices')->onDelete('cascade');
                $table->integer('user_gprs_template_id')->unsigned()->index();
                $table->foreign('user_gprs_template_id')->references('id')->on('user_gprs_templates')->onDelete('cascade');
                $table->primary(['device_id', 'user_gprs_template_id'], 'user_gprs_template_devices_pk');
            });
        }

        DB::statement("
         INSERT IGNORE INTO user_gprs_template_devices
                     SELECT *
                       FROM command_template_devices ctd
                      WHERE (SELECT ct.type FROM command_templates ct WHERE ct.id = ctd.command_template_id) = 'gprs'");
    }

    private function revertDeviceTypeRelations()
    {
        if (!Schema::hasTable('user_gprs_template_device_types')) {
            Schema::create('user_gprs_template_device_types', function (Blueprint $table) {
                $table->integer('device_type_id')->unsigned()->index();
                $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('cascade');
                $table->integer('user_gprs_template_id')->unsigned()->index();
                $table->foreign('user_gprs_template_id')->references('id')->on('user_gprs_templates')->onDelete('cascade');
                $table->primary(['device_type_id', 'user_gprs_template_id'], 'user_gprs_template_device_types_pk');
            });
        }

        DB::statement("
         INSERT IGNORE INTO user_gprs_template_device_types
                     SELECT *
                       FROM command_template_device_types ctdt
                      WHERE (SELECT ct.type FROM command_templates ct WHERE ct.id = ctdt.command_template_id) = 'gprs'");
    }
}
