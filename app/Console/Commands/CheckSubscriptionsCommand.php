<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Tobuli\Entities\Subscription;
use Tobuli\Helpers\Payments\Payments;


class CheckSubscriptionsCommand extends Command
{
    const SUB_DAYS = 14;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'subscriptions:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle expired subscriptions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $subscriptions = Subscription::subscribable()
            ->where('active', 1)
            ->where('expiration_date', '<', Carbon::now())
            ->where('expiration_date', '>', Carbon::now()->subDays(self::SUB_DAYS))
            ->with('user', 'order')
            ->get();

        if ($subscriptions->isEmpty()) {
            echo 'Done';
            return;
        }

        $payments = new Payments();

        foreach ($subscriptions as $subscription) {
            if (is_null($subscription->user) || is_null($subscription->order)) {
                $subscription->cancel();
                continue;
            }

            $payments->setGateway($subscription->gateway);

            if ( ! $payments->isSubscriptionRenewed($subscription))
                continue;

            try {
                $payments->renewSubscription($subscription, $payments->getSubscriptionEnd($subscription));
            } catch (\Exception $e) {
                $this->error("Failing renew subscription #{$subscription->id} {$subscription->gateway} $subscription->gateway_id with error: " . $e->getMessage());
            }
        }

        echo 'Done';
    }
}
