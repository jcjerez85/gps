<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Tobuli\Entities\Order;
use Tobuli\Entities\Subscription;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')
                ->unsigned()
                ->nullable()
                ->index();
            $table->integer('plan_id')
                ->nullable()
                ->unsigned();
            $table->string('plan_type');
            $table->decimal('price', 8, 2)
                ->nullable();
            $table->integer('entity_id')
                ->nullable()
                ->unsigned();
            $table->string('entity_type');
            $table->dateTime('paid_at')
                ->nullable()
                ->default(null);
            $table->timestamps();
        });

        Schema::table('subscriptions', function ($table) {
            $table->integer('order_id')
                ->unsigned()
                ->nullable()
                ->after('gateway_id');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('set null');
        });

        foreach (Subscription::all() as $subscription) {
            $order = Order::create([
                'user_id'         => $subscription->user_id,
                'plan_id'         => $subscription->billing_plan_id,
                'plan_type'       => 'billing_plan',
                'entity_id'       => $subscription->user_id,
                'entity_type'     => 'user',
            ]);

            $subscription->update([
                'order_id' => $order->id,
            ]);
        }

        Schema::table('subscriptions', function ($table) {
            $table->dropForeign(['billing_plan_id']);
        });

        Schema::table('subscriptions', function ($table) {
            $table->dropColumn('billing_plan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::drop('orders');
    }
}
