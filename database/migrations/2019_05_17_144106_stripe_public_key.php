<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StripePublicKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! empty(settings('payments.stripe.public_key'))) return;

        $old = settings('main_settings.stripe_public_key');

        if (empty($old)) return;

        settings('payments.stripe.public_key', $old);
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
