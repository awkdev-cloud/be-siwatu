<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('package_type')->default('Paket Individual');
            $table->string('highlight_label')->nullable();

            $table->text('description');
            $table->json('facilities')->nullable();

            $table->string('duration')->nullable();
            $table->integer('price')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('is_active');
            $table->index('is_featured');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};