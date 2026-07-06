<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'package_type',
        'highlight_label',
        'description',
        'facilities',
        'duration',
        'price',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'facilities' => 'array',
        'price' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
}