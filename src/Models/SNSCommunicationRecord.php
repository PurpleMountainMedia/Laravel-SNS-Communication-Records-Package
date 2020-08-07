<?php

namespace PurpleMountain\SNSCommunicationRecords\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsEmail;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsMarketing;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsPhone;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsSMS;
use PurpleMountain\SNSCommunicationRecords\Concerns\LogAsTransactional;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationBounced;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationClicked;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationComplaint;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationDelivered;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationOpened;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationRecordCreated;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationRecordDeleted;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationRecordUpdated;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationRejected;
use PurpleMountain\SNSCommunicationRecords\Events\SNSCommunicationSent;

class SNSCommunicationRecord extends Model
{
    /**
     * The table that this model relates to.
     *
     * @return string
     */
    public $table = 'sns_communication_records';

    /**
     * Whether the ID collumn in auto incrementing.
     *
     * @return boolean
     */
    public $incrementing = false;

    /**
     * What the ID collumn key type is.
     *
     * @return string
     */
    protected $keyType = 'string';

    /**
     * The relationships to load by default.
     *
     * @return array
     */
    protected $with = ['to'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_id', 'to_id', 'to_type', 'type', 'subject',
        'content', 'sent_at', 'delivered_at', 'read_at', 'clicked_at',
        'bounced_at', 'complaint_at', 'rejected_at', 'from',
        'bcc', 'cc', 'reply_to', 'content_type', 'extra',
        'internal_ref', 'internal_id', 'name'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'complaint_at' => 'datetime',
        'rejected_at' => 'datetime',
        'extra' => 'array'
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SNSCommunicationRecordCreated::class,
        'updated' => SNSCommunicationRecordUpdated::class,
        'deleted' => SNSCommunicationRecordDeleted::class
    ];

    /**
     * Hook into the boot method on the model and register any event listeners.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::orderedUuid();
        });
    }

    /**
     * Get the types of communcations we want to record.
     *
     * @return array
     */
    public static function types(): array
    {
        return [
            LogAsEmail::class => 'Email',
            LogAsTransactional::class => 'Transactional Email',
            LogAsMarketing::class => 'Marketing Email',
            LogAsSMS::class => 'SMS Message',
            LogAsPhone::class => 'Phone Call',
        ];
    }

    /**
     * Create a record of the communication from the message sent event.
     *
     * @param  \Illuminate\Mail\Events\MessageSent $event
     * @param  string $toEmail
     * @param  string $toName
     *
     * @return \PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord
     */
    public static function createFromSentEvent($event, $toEmail, $toName, $type, $name, $internalId, $sesId)
    {
        $message = $event->message;
        $user = config('snscommunicationrecords.to_class')::whereEmail($toEmail)->first();

        return self::create([
            'channel_id' => $sesId,
            'internal_id' => $internalId,
            'internal_ref' => $message->getId(),
            'to_id' => optional($user)->id,
            'to_type' => $user ? get_class($user) : null,
            'from' => array_key_first($message->getFrom() ?: []),
            'bcc' => array_key_first($message->getBcc() ?: []),
            'cc' => array_key_first($message->getCc() ?: []),
            'reply_to' => array_key_first($message->getReplyTo() ?: []),
            'content_type' => $message->getContentType(),
            'type' => $type,
            'name' => $name,
            'subject' => $message->getSubject(),
            'content' => $message->getBody()
        ]);
    }

    /**
     * Get the owning commentable model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function to(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Fire an event when this communication has been sent.
     *
     * @return \App\CommunicationRecord
     */
    public function fireSentEvent()
    {
        event(new SNSCommunicationSent($this));
    }

    /**
     * Fire an event when this communication has been delivered.
     *
     * @return \App\CommunicationRecord
     */
    public function fireDeliveredEvent()
    {
        event(new SNSCommunicationDelivered($this));
    }

    /**
     * Fire an event when this communication has been opened.
     *
     * @return \App\CommunicationRecord
     */
    public function fireOpenEvent()
    {
        event(new SNSCommunicationOpened($this));
    }

    /**
     * Fire an event when this communication has been clicked.
     *
     * @return \App\CommunicationRecord
     */
    public function fireClickEvent()
    {
        event(new SNSCommunicationClicked($this));
    }

    /**
     * Fire an event when this communication has been clicked.
     *
     * @return \App\CommunicationRecord
     */
    public function fireBounceEvent()
    {
        event(new SNSCommunicationBounced($this));
    }

    /**
     * Fire an event when this communication has been clicked.
     *
     * @return \App\CommunicationRecord
     */
    public function fireComplaintEvent()
    {
        event(new SNSCommunicationComplaint($this));
    }

    /**
     * Fire an event when this communication has been clicked.
     *
     * @return \App\CommunicationRecord
     */
    public function fireRejectEvent()
    {
        event(new SNSCommunicationRejected($this));
    }

}
