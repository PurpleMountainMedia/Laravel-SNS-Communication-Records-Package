<?php

namespace PurpleMountain\SNSCommunicationRecords\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord as SNSCommunicationRecordModel;

class SNSCommunicationRecord extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'channel_id' => $this->channel_id,
            'internal_id' => $this->internal_id,
            'internal_ref' => $this->internal_ref,
            'type' => $this->type,
            'type_label' => SNSCommunicationRecordModel::types()[$this->type],
            'name' => $this->name,
            'subject' => $this->subject,
            'sent_at' => $this->sent_at,
            'delivered_at' => $this->delivered_at,
            'read_at' => $this->read_at,
            'clicked_at' => $this->clicked_at,
            'bounced_at' => $this->bounced_at,
            'complaint_at' => $this->complaint_at,
            'rejected_at' => $this->rejected_at,
            'to' => new SNSCommunicationRecordTo($this->to),
            'from_email' => $this->from,
            'bcc_email' => $this->bcc,
            'cc_email' => $this->cc,
            'reply_to_email' => $this->reply_to,
            'content_type' => $this->content_type,
            'extra' => $this->extra
        ];
    }
}
