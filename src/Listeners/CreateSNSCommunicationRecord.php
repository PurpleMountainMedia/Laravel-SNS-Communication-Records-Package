<?php

namespace PurpleMountain\SNSCommunicationRecords\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsEmail;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogNotification;
use PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord;

class CreateSNSCommunicationRecord implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->data['__laravel_notification'] ?? false) {
            $implements = class_implements($event->data['__laravel_notification']);
            $name = Str::afterLast($event->data['__laravel_notification'], '\\');
            $internalId = $event->data['__laravel_notification_id'];
            $sesId = $event->message->getHeaders()->get('X-SES-Message-ID')->getValue();

            if (Arr::has($implements, LogNotification::class)) {
                // Work out the type of notification this is.
                $type = Arr::first(array_keys(SNSCommunicationRecord::types()), function ($type) use ($implements) {
                    return Arr::has($implements, $type);
                }, LogAsEmail::class);

                foreach ($event->message->getTo() as $toEmail => $toName) {
                    config('snscommunicationrecords.record_class')::createFromSentEvent($event, $toEmail, $toName, $type, $name, $internalId, $sesId);
                }
            }
        }
    }
}
