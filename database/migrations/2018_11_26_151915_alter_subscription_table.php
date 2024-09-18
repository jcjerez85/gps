<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasColumn('subscriptions', 'user_id')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropColumn(['period_name', 'devices_limit', 'days', 'trial', 'name']);

                $table->boolean('active')->default(false)->after('id');
                $table->timestamp('expiration_date')->nullable(true)->after('id');
                $table->string('gateway_id')->after('id');
                $table->string('gateway')->after('id');
                $table->integer('user_id')->unsigned()->nullable(false)->after('id');
                $table->integer('billing_plan_id')->unsigned()->nullable(false)->after('id');
            });
        }

        try {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('billing_plan_id')->references('id')->on('billing_plans')->onDelete('cascade');
            });
        } catch(Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // TODO Add droped columns on rollback??
        if ( ! Schema::hasColumn('subscriptions', 'user_id')) return;

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['expiration_date', 'gateway_id', 'gateway', 'billing_plan_id', 'user_id', 'active']);
        });
    }
}
