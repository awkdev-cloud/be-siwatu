<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();

            $table->string('whatsapp_title')->default('WhatsApp Admin');
            $table->string('whatsapp_number');
            $table->string('whatsapp_display')->nullable();
            $table->string('whatsapp_response_time')->nullable();
            $table->text('whatsapp_message')->nullable();

            $table->string('operational_title')->default('Jam Operasional');
            $table->string('operational_hours')->nullable();
            $table->string('operational_note')->nullable();

            $table->string('location_title')->default('Lokasi Wisata');
            $table->string('location_name')->nullable();
            $table->text('location_address')->nullable();
            $table->string('google_maps_url')->nullable();
            $table->text('google_maps_embed_url')->nullable();

            $table->string('helper_title')->nullable();
            $table->text('helper_description')->nullable();
            $table->string('helper_button_text')->default('Chat WhatsApp');

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_settings');
    }
};