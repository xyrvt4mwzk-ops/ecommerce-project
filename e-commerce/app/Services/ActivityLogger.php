<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function record(string $event, ?Model $subject = null, array $properties = [], ?Request $request = null): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'event' => $event, 'subject_type' => $subject?->getMorphClass(), 'subject_id' => $subject?->getKey(), 'ip_address' => $request?->ip() ?? request()?->ip(), 'user_agent' => substr((string) ($request?->userAgent() ?? request()?->userAgent()), 0, 1000), 'properties' => $properties]);
    }
}
