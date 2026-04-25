<?php

namespace Modules\Support\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class SupportServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Support';
    protected string $nameLower = 'support';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    protected function registerAliases(): void
    {
        $aliases = [
            \App\Models\SupportTicket::class        => \Modules\Support\Models\SupportTicket::class,
            \App\Models\SupportTicketMessage::class => \Modules\Support\Models\SupportTicketMessage::class,
            \App\Models\ChatMessage::class          => \Modules\Support\Models\ChatMessage::class,
            \App\Models\ContactRequest::class       => \Modules\Support\Models\ContactRequest::class,
        ];

        foreach ($aliases as $alias => $concrete) {
            if (!class_exists($alias)) {
                class_alias($concrete, $alias);
            }
        }
    }

    public function register(): void
    {
        parent::register();
        $this->registerAliases();
    }
}
