<?php

namespace PurpleMountain\SNSCommunicationRecords\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord;

class SNSCommunicationClicked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The SNS communication record model.
     *
     * @var \PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord
     */
    protected $snsCommunicationRecord;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SNSCommunicationRecord $snsCommunicationRecord)
    {
        $this->snsCommunicationRecord = $snsCommunicationRecord;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('sns-communication-records');
    }
}
