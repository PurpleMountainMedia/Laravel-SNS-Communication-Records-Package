<?php

namespace PurpleMountain\SNSCommunicationRecords\Http\Controllers;

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PurpleMountain\SNSCommunicationRecords\Jobs\LogSNSCommunicationEvent;
use PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord;

class SNSCommunicationsWebhookController extends Controller
{
    /**
     * The entire message sent from ses.
     *
     * @var array
     */
    protected $message;

    /**
     * The type of message / event.
     *
     * @var string
     */
    protected $messageType;

    /**
     * If this message was a mail, this is that mail object.
     *
     * @var array
     */
    protected $messageMail;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $snsMessage = Message::fromJsonString($request->getContent());
        $validator = new MessageValidator();

        if ($validator->isValid($snsMessage)) {
            // Format the incoming message
            $eventData = $snsMessage->toArray();
            $this->message = json_decode($eventData['Message'], true);
            $this->messageType = $this->message['eventType'] ?? null;
            $this->messageMail = $this->message['mail'] ?? null;

            // If this is the first request from AWS, we have to subscribe to the topic
            if ($eventData['SubscribeURL'] ?? null) {
                Http::get($eventData['SubscribeURL']);
            }

            // If this is an email event
            if ($this->messageMail) {
                // Find the communication record for this email in our database
                $snsCommunicationRecords = config('snscommunicationrecords.record_class')::whereChannelId($this->messageMail['messageId'])->get();

                // Calculate the method that needs to be called for this event
                $method = 'handle' . ucfirst(Str::camel(Str::replaceFirst('.', '_', $this->messageType)));

                // Foreach communication record, call the correct method
                if (method_exists($this, $method) && $snsCommunicationRecords->count() > 0) {
                    foreach ($snsCommunicationRecords as $snsCommunicationRecord) {
                        $this->{$method}($snsCommunicationRecord, $eventData);
                    }
                }
            }
        }

        return new Response;
    }

    /**
     * Handle the send event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleSend(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_SEND', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the delivery event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleDelivery(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_DELIVERY', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the open event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleOpen(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_OPEN', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the click event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleClick(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_CLICK', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the bounce event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleBounce(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_BOUNCE', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the complaint event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleComplaint(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_COMPLAINT', $snsCommunicationRecord, $eventData);
    }

    /**
     * Handle the reject event from SES.
     *
     * @param \PurpleMountain\SNSCommunicationRecords\Models $snsCommunicationRecord
     * @param array $eventData
     * @return void
     */
    protected function handleReject(SNSCommunicationRecord $snsCommunicationRecord, $eventData)
    {
        LogSNSCommunicationEvent::dispatch('EVENT_REJECT', $snsCommunicationRecord, $eventData);
    }
}
