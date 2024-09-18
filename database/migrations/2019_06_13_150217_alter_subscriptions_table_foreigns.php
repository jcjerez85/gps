<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionsTableForeigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `subscriptions` CHANGE `user_id` `user_id` INT(10) UNSIGNED;');
        DB::statement('ALTER TABLE `subscriptions` CHANGE `billing_plan_id` `billing_plan_id` INT(10) UNSIGNED;');

        try {
            Schema::table('subscriptions', function($table) {
                $table->dropForeign('subscriptions_user_id_foreign');
            });
        } catch (Exception $e) {}

        try {
            Schema::table('subscriptions', function($table) {
                $table->dropForeign('subscriptions_billing_plan_id_foreign');
            });
        } catch (Exception $e) {}

        Schema::table('subscriptions', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('billing_plan_id')->references('id')->on('billing_plans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
