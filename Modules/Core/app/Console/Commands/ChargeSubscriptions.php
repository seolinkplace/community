<?php

namespace Modules\Core\Console\Commands;

use Modules\Core\Services\SubscriptionService;
use Illuminate\Console\Command;

class ChargeSubscriptions extends Command
{
    protected $signature = 'subscriptions:charge';
    protected $description = 'Charge monthly subscriptions and expire grace period subscriptions';

    public function __construct(private readonly SubscriptionService $subscriptionService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->subscriptionService->chargeAllDue();
        $this->subscriptionService->expireGrace();

        $this->info('Subscriptions charged and grace periods updated.');

        return self::SUCCESS;
    }
}
