<?php

namespace Database\Seeders;

use App\Models\PlantRevitalization;
use Illuminate\Database\Seeder;

class PlantRevitalizationSeeder extends Seeder
{
    public function run(): void
    {
        PlantRevitalization::updateOrCreate(
            ['name' => 'Pohon Gayam'],
            [
                'scientific_name' => 'Inocarpus fagifer',
                'image' => null,
                'description' => 'Tanaman peneduh yang dapat mendukung revitalisasi kawasan wisata agar lebih asri dan nyaman bagi pengunjung.',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );
    }
}