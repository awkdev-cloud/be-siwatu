<?php

namespace Database\Seeders;

use App\Models\ContactSetting;
use Illuminate\Database\Seeder;

class ContactSettingSeeder extends Seeder
{
    public function run(): void
    {
        ContactSetting::updateOrCreate(
            ['id' => 1],
            [
                'whatsapp_title' => 'WhatsApp Admin',
                'whatsapp_number' => '6281234567890',
                'whatsapp_display' => '+62 XXX-XXX-XXX',
                'whatsapp_response_time' => 'Response cepat, 08.00–17.00 WIB',
                'whatsapp_message' => 'Halo Admin Siwatu, saya ingin bertanya tentang wisata Alas Watu Kebonan.',

                'operational_title' => 'Jam Operasional',
                'operational_hours' => '08.00 – 17.00 WIB',
                'operational_note' => 'Senin – Minggu, termasuk hari libur',

                'location_title' => 'Lokasi Wisata',
                'location_name' => 'Desa Kebonan',
                'location_address' => 'Desa Kebonan, Kecamatan Karanggede, Kabupaten Boyolali',
                'google_maps_url' => 'https://maps.google.com',
                'google_maps_embed_url' => null,

                'helper_title' => 'Siwatu siap membantumu!',
                'helper_description' => 'Apa pun pertanyaanmu tentang Alas Watu Kebonan, hubungi admin kami via WhatsApp. Kami dengan senang hati membantumu merencanakan kunjunganmu.',
                'helper_button_text' => 'Chat WhatsApp',

                'is_active' => true,
            ]
        );
    }
}