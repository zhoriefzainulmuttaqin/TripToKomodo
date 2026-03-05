<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'session_id',
        'event_type',
        'page_path',
        'page_url',
        'referrer',
        'source_channel',
        'source_detail',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'country_code',
        'device_type',
        'browser',
        'contact_target',
        'engagement_seconds',
        'ip_address',
        'user_agent',
        'occurred_at',
    ];

    protected $casts = [
        'engagement_seconds' => 'integer',
        'occurred_at' => 'datetime',
    ];
}
