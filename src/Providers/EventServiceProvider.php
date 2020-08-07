<?php

namespace PurpleMountain\SNSCommunicationRecords\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseProvider;
use Illuminate\Mail\Events\MessageSent;
use PurpleMountain\SNSCommunicationRecords\Listeners\CreateSNSCommunicationRecord;
use PurpleMountain\SNSCommunicationRecords\Listeners\CreateSNSCommunicationRecordTest;

class EventServiceProvider extends BaseProvider
{
    /**
     * The event listener mappings for the package.
     *
     * @var array
     */
    protected $listen = [
        MessageSent::class => [
            CreateSNSCommunicationRecord::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}