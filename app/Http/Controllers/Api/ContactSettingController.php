<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use App\Models\SocialLink;
use Illuminate\Http\Request;

class ContactSettingController extends Controller
{
    public function show()
    {
        $contact = ContactSetting::where('is_active', true)->first();

        if (!$contact) {
            return response()->json([
                'message' => 'Data kontak belum tersedia.',
                'data' => null,
            ], 404);
        }

        $socialLinks = SocialLink::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($socialLink) {
                return $this->formatSocialLink($socialLink);
            });

        return response()->json([
            'message' => 'Data kontak berhasil diambil.',
            'data' => $this->formatContact($contact, $socialLinks),
        ]);
    }

    public function adminShow()
    {
        $contact = ContactSetting::firstOrCreate(
            ['id' => 1],
            [
                'whatsapp_title' => 'WhatsApp Admin',
                'whatsapp_number' => '6281234567890',
                'is_active' => true,
            ]
        );

        return response()->json([
            'message' => 'Data kontak admin berhasil diambil.',
            'data' => $this->formatContact($contact),
        ]);
    }

    public function update(Request $request)
    {
        $contact = ContactSetting::firstOrCreate(
            ['id' => 1],
            [
                'whatsapp_title' => 'WhatsApp Admin',
                'whatsapp_number' => '6281234567890',
                'is_active' => true,
            ]
        );

        $validated = $request->validate([
            'whatsapp_title' => ['nullable', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'whatsapp_display' => ['nullable', 'string', 'max:255'],
            'whatsapp_response_time' => ['nullable', 'string', 'max:255'],
            'whatsapp_message' => ['nullable', 'string'],

            'operational_title' => ['nullable', 'string', 'max:255'],
            'operational_hours' => ['nullable', 'string', 'max:255'],
            'operational_note' => ['nullable', 'string', 'max:255'],

            'location_title' => ['nullable', 'string', 'max:255'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'location_address' => ['nullable', 'string'],
            'google_maps_url' => ['nullable', 'url'],
            'google_maps_embed_url' => ['nullable', 'string'],

            'helper_title' => ['nullable', 'string', 'max:255'],
            'helper_description' => ['nullable', 'string'],
            'helper_button_text' => ['nullable', 'string', 'max:255'],

            'is_active' => ['nullable', 'boolean'],
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Data kontak berhasil diperbarui.',
            'data' => $this->formatContact($contact),
        ]);
    }

    private function formatContact(ContactSetting $contact, $socialLinks = null): array
    {
        return [
            'id' => $contact->id,

            'whatsapp' => [
                'title' => $contact->whatsapp_title,
                'number' => $contact->whatsapp_number,
                'display' => $contact->whatsapp_display,
                'response_time' => $contact->whatsapp_response_time,
                'message' => $contact->whatsapp_message,
                'link' => $this->generateWhatsappLink(
                    $contact->whatsapp_number,
                    $contact->whatsapp_message
                ),
            ],

            'operational' => [
                'title' => $contact->operational_title,
                'hours' => $contact->operational_hours,
                'note' => $contact->operational_note,
            ],

            'location' => [
                'title' => $contact->location_title,
                'name' => $contact->location_name,
                'address' => $contact->location_address,
                'google_maps_url' => $contact->google_maps_url,
                'google_maps_embed_url' => $contact->google_maps_embed_url,
            ],

            'helper' => [
                'title' => $contact->helper_title,
                'description' => $contact->helper_description,
                'button_text' => $contact->helper_button_text,
                'button_link' => $this->generateWhatsappLink(
                    $contact->whatsapp_number,
                    $contact->whatsapp_message
                ),
            ],

            'social_links' => $socialLinks,

            'is_active' => $contact->is_active,
            'created_at' => $contact->created_at,
            'updated_at' => $contact->updated_at,
        ];
    }

    private function formatSocialLink(SocialLink $socialLink): array
    {
        return [
            'id' => $socialLink->id,
            'platform' => $socialLink->platform,
            'username' => $socialLink->username,
            'url' => $socialLink->url,
            'icon' => $socialLink->icon,
            'is_active' => $socialLink->is_active,
            'sort_order' => $socialLink->sort_order,
        ];
    }

    private function generateWhatsappLink(?string $number, ?string $message): ?string
    {
        if (!$number) {
            return null;
        }

        $cleanNumber = preg_replace('/[^0-9]/', '', $number);
        $encodedMessage = urlencode($message ?? '');

        return "https://wa.me/{$cleanNumber}?text={$encodedMessage}";
    }
}