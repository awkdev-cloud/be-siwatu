<?php

namespace Database\Seeders;

use App\Models\UniqueRock;
use Illuminate\Database\Seeder;

class UniqueRockSeeder extends Seeder
{
    public function run(): void
    {
        UniqueRock::updateOrCreate(
            ['name' => 'Batu Andesit Alas Watu'],
            [
                'scientific_name' => 'Andesite',
                'image' => null,
                'description' => 'Batuan vulkanik bertekstur padat yang menjadi salah satu karakter visual kawasan Alas Watu Kebonan.',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );
    }
}