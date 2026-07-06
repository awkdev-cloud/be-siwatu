<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'name',
        'rating',
        'comment',
        'review_date',
        'is_published',
    ];

    protected $casts = [
        'rating' => 'integer',
        'review_date' => 'date',
        'is_published' => 'boolean',
    ];
}