<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'gallery_category_id',
        'title',
        'slug',
        'description',
        'image',
        'is_published',
    ];

    public function category()
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}