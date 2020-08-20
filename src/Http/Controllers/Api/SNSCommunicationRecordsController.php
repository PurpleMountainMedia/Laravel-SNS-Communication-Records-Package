<?php

namespace PurpleMountain\SNSCommunicationRecords\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use PurpleMountain\SNSCommunicationRecords\Http\Controllers\Controller;
use PurpleMountain\SNSCommunicationRecords\Http\Resources\SNSCommunicationRecord as SNSCommunicationRecordResource;

class SNSCommunicationRecordsController extends Controller
{
    /**
     * Return a list of communication records
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        Gate::authorize('view-any-communication-records');

        return SNSCommunicationRecordResource::collection(
            config('snscommunicationrecords.record_class')::with($request->with ?: [])
                ->orderByDesc('created_at')
                ->paginate()
        );
    }
}
