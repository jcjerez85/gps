<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentsGatewayConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $settings_to_migrate = [
            'main_settings.paypal_client_id'    => 'payments.paypal.client_id',
            'main_settings.paypal_secret'       => 'payments.paypal.secret',
            'main_settings.paypal_currency'     => 'payments.paypal.currency',
            'main_settings.paypal_payment_name' => 'payments.paypal.payment_name',
            'main_settings.stripe_secret_key'   => 'payments.stripe.secret_key',
            'main_settings.stripe_currency'     => 'payments.stripe.currency'
        ];

        foreach ($settings_to_migrate as $old => $new)
        {
            if ( ! empty(settings($new)))
                continue;

            $value = settings($old);

            if (empty($value))
                continue;

            settings($new, $value);
        }

        if (settings('payment_type') == 'paypal')
            settings('payments.gateway.paypal', 1);

        if (settings('payment_type') == 'stripe')
            settings('payments.gateway.stripe', 1);
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
