<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            GalleryCategorySeeder::class,
            ReviewSeeder::class,
            TourPackageSeeder::class,
            ContactSettingSeeder::class,
            SocialLinkSeeder::class,
            UniqueRockSeeder::class,
            PlantRevitalizationSeeder::class,
        ]);
    }
}