<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = [
            [
                'name' => 'Sari Dewi Pratiwi',
                'rating' => 5,
                'comment' => 'Spot fotonya keren banget! Alam yang masih asri dan batu-batu unik jadi latar foto yang tidak ada duanya. Pasti balik lagi bersama keluarga!',
                'review_date' => '2026-02-28',
                'is_published' => true,
            ],
            [
                'name' => 'Andi Saputra',
                'rating' => 5,
                'comment' => 'Tempatnya nyaman, suasananya alami, dan cocok untuk wisata keluarga maupun kegiatan edukasi alam.',
                'review_date' => '2026-03-10',
                'is_published' => true,
            ],
            [
                'name' => 'Rina Lestari',
                'rating' => 4,
                'comment' => 'Pemandangannya bagus dan banyak spot menarik. Fasilitas bisa terus ditingkatkan agar pengunjung lebih nyaman.',
                'review_date' => '2026-03-15',
                'is_published' => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::updateOrCreate(
                [
                    'name' => $review['name'],
                    'review_date' => $review['review_date'],
                ],
                $review
            );
        }
    }
}