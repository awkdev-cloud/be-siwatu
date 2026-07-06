<?php

namespace Database\Seeders;

use App\Models\TourPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title' => 'Paket Kunjungan Reguler',
                'package_type' => 'Paket Individual',
                'highlight_label' => 'Cocok untuk Keluarga',
                'description' => 'Nikmati keindahan alam Alas Watu Kebonan dengan panduan wisata lokal yang berpengalaman.',
                'facilities' => [
                    'Tiket masuk',
                    'Pemandu wisata lokal',
                    'Air minum',
                    'Dokumentasi foto',
                ],
                'duration' => '2–3 jam',
                'price' => 25000,
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Paket Edukasi Alam',
                'package_type' => 'Paket Edukasi',
                'highlight_label' => 'Cocok untuk Pelajar',
                'description' => 'Paket wisata edukatif untuk mengenal potensi alam, batuan, dan tanaman khas kawasan Alas Watu Kebonan.',
                'facilities' => [
                    'Tiket masuk',
                    'Pemandu edukasi',
                    'Materi pengenalan alam',
                    'Dokumentasi kegiatan',
                ],
                'duration' => '3–4 jam',
                'price' => 35000,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ],
        ];

        foreach ($packages as $package) {
            TourPackage::updateOrCreate(
                ['slug' => Str::slug($package['title'])],
                [
                    'title' => $package['title'],
                    'slug' => Str::slug($package['title']),
                    'package_type' => $package['package_type'],
                    'highlight_label' => $package['highlight_label'],
                    'description' => $package['description'],
                    'facilities' => $package['facilities'],
                    'duration' => $package['duration'],
                    'price' => $package['price'],
                    'is_active' => $package['is_active'],
                    'is_featured' => $package['is_featured'],
                    'sort_order' => $package['sort_order'],
                ]
            );
        }
    }
}