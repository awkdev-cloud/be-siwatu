<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryCategory;
use App\Models\PlantRevitalization;
use App\Models\Review;
use App\Models\SocialLink;
use App\Models\TourPackage;
use App\Models\UniqueRock;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'message' => 'Ringkasan dashboard berhasil diambil.',
            'data' => [
                'galleries' => [
                    'total' => Gallery::count(),
                    'published' => Gallery::where(
                        'is_published',
                        true
                    )->count(),
                    'draft' => Gallery::where(
                        'is_published',
                        false
                    )->count(),
                ],

                'gallery_categories' => [
                    'total' => GalleryCategory::count(),
                    'active' => GalleryCategory::where(
                        'is_active',
                        true
                    )->count(),
                    'inactive' => GalleryCategory::where(
                        'is_active',
                        false
                    )->count(),
                ],

                'reviews' => [
                    'total' => Review::count(),
                    'published' => Review::where(
                        'is_published',
                        true
                    )->count(),
                    'draft' => Review::where(
                        'is_published',
                        false
                    )->count(),
                ],

                'tour_packages' => [
                    'total' => TourPackage::count(),
                    'active' => TourPackage::where(
                        'is_active',
                        true
                    )->count(),
                    'inactive' => TourPackage::where(
                        'is_active',
                        false
                    )->count(),
                    'featured' => TourPackage::where(
                        'is_featured',
                        true
                    )->count(),
                ],

                'social_links' => [
                    'total' => SocialLink::count(),
                    'active' => SocialLink::where(
                        'is_active',
                        true
                    )->count(),
                    'inactive' => SocialLink::where(
                        'is_active',
                        false
                    )->count(),
                ],

                'unique_rocks' => [
                    'total' => UniqueRock::count(),
                    'published' => UniqueRock::where(
                        'is_published',
                        true
                    )->count(),
                    'draft' => UniqueRock::where(
                        'is_published',
                        false
                    )->count(),
                ],

                'plant_revitalizations' => [
                    'total' => PlantRevitalization::count(),
                    'published' => PlantRevitalization::where(
                        'is_published',
                        true
                    )->count(),
                    'draft' => PlantRevitalization::where(
                        'is_published',
                        false
                    )->count(),
                ],
            ],
        ]);
    }
}