<?php

namespace Database\Seeders;

use App\Models\GalleryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GalleryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tanaman',
                'description' => 'Dokumentasi tanaman dan vegetasi yang ada di kawasan wisata.',
            ],
            [
                'name' => 'Batuan',
                'description' => 'Dokumentasi batuan khas dan elemen alam di kawasan wisata.',
            ],
            [
                'name' => 'Kegiatan',
                'description' => 'Dokumentasi kegiatan wisata, kunjungan, dan aktivitas masyarakat.',
            ],
            [
                'name' => 'Fasilitas',
                'description' => 'Dokumentasi fasilitas pendukung wisata.',
            ],
        ];

        foreach ($categories as $category) {
            GalleryCategory::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}