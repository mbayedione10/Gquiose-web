<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\SendArticleNotification;
use App\Listeners\SendHealthCenterNotification;
use App\Listeners\SendQuizNotification;
use App\Listeners\SendVideoNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\NewArticlePublished::class => [
            \App\Listeners\SendArticleNotification::class,
        ],
        \App\Events\NewQuizPublished::class => [
            \App\Listeners\SendQuizNotification::class,
        ],
        \App\Events\NewVideoPublished::class => [
            \App\Listeners\SendVideoNotification::class,
        ],
        \App\Events\NewHealthCenterAdded::class => [
            \App\Listeners\SendHealthCenterNotification::class,
        ],
        \App\Events\MessageReplied::class => [
            \App\Listeners\SendReplyNotification::class,
        ],
        \App\Events\UserMentioned::class => [
            \App\Listeners\SendMentionNotification::class,
        ],
        \App\Events\CycleReminderTriggered::class => [
            \App\Listeners\SendCycleReminderNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}