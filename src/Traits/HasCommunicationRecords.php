<?php

namespace PurpleMountain\SNSCommunicationRecords\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasCommunicationRecords
{
    /**
     * The communication records for this record.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function communicationRecords(): MorphOne
    {
        return $this->morphOne(config('snscommunicationrecords.record_class'), 'to');
    }
}