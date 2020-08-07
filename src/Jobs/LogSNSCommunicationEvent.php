<?php

namespace PurpleMountain\SNSCommunicationRecords\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord;

class LogSNSCommunicationEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The type of event this is logging.
     *
     * @var string
     */
    protected $eventType;

    /**
     * The communication record associated with this event.
     *
     * @var \App\CommunicationRecord
     */
    protected $snsCommunicationRecord;

    /**
     * The event data sent from the provider.
     *
     * @var array
     */
    protected $eventData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($eventType, $snsCommunicationRecord, $eventData)
    {
        $this->eventType = $eventType;
        $this->snsCommunicationRecord = $snsCommunicationRecord;
        $this->eventData = $eventData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->eventType === 'EVENT_SEND') {
            $this->snsCommunicationRecord->update([
                'sent_at' => now()
            ]);
            $this->snsCommunicationRecord->fireSentEvent();
        }
        if ($this->eventType === 'EVENT_DELIVERY') {
            $this->snsCommunicationRecord->update([
                'delivered_at' => now()
            ]);
            $this->snsCommunicationRecord->fireDeliveredEvent();
        }
        if ($this->eventType === 'EVENT_OPEN') {
            $this->snsCommunicationRecord->update([
                'read_at' => now()
            ]);
            $this->snsCommunicationRecord->fireOpenEvent();
        }
        if ($this->eventType === 'EVENT_CLICK') {
            $this->snsCommunicationRecord->update([
                'clicked_at' => now()
            ]);
            $this->snsCommunicationRecord->fireClickEvent();
        }
        if ($this->eventType === 'EVENT_BOUNCE') {
            $this->snsCommunicationRecord->update([
                'bounced_at' => now()
            ]);
            $this->snsCommunicationRecord->fireBounceEvent();
        }
        if ($this->eventType === 'EVENT_COMPLAINT') {
            $this->snsCommunicationRecord->update([
                'complaint_at' => now()
            ]);
            $this->snsCommunicationRecord->fireComplaintEvent();
        }
        if ($this->eventType === 'EVENT_REJECT') {
            $this->snsCommunicationRecord->update([
                'rejected_at' => now()
            ]);
            $this->snsCommunicationRecord->fireRejectEvent();
        }
    }
}
