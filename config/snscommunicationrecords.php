<?php

return [
    'record_class' => PurpleMountain\SNSCommunicationRecords\Models\SNSCommunicationRecord::class,

    'to_class' => App\User::class,

    'api_middleware' => ['auth:api']
];