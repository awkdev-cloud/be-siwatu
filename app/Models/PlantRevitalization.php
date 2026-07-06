<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantRevitalization extends Model
{
    protected $fillable = [
        'name',
        'scientific_name',
        'image',
        'description',
        'qr_code',
        'qr_target_url',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];
}