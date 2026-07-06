<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plant_revitalizations', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('scientific_name')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();

            $table->string('qr_code')->nullable();
            $table->string('qr_target_url')->nullable();

            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('is_published');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plant_revitalizations');
    }
};