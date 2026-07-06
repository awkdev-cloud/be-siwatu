<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'platform' => 'Instagram',
                'username' => '@alaswatukebonan',
                'url' => 'https://instagram.com/alaswatukebonan',
                'icon' => 'instagram',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'platform' => 'YouTube',
                'username' => 'Alas Watu Kebonan',
                'url' => 'https://youtube.com',
                'icon' => 'youtube',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'platform' => 'Website',
                'username' => 'alaswatukebonan.id',
                'url' => 'https://alaswatukebonan.id',
                'icon' => 'website',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($socialLinks as $socialLink) {
            SocialLink::updateOrCreate(
                [
                    'platform' => $socialLink['platform'],
                ],
                $socialLink
            );
        }
    }
}