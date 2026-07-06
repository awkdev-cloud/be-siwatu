<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    protected $fillable = [
        'whatsapp_title',
        'whatsapp_number',
        'whatsapp_display',
        'whatsapp_response_time',
        'whatsapp_message',

        'operational_title',
        'operational_hours',
        'operational_note',

        'location_title',
        'location_name',
        'location_address',
        'google_maps_url',
        'google_maps_embed_url',

        'helper_title',
        'helper_description',
        'helper_button_text',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}