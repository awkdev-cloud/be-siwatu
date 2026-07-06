<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('gallery_category_id')
                ->constrained('gallery_categories')
                ->restrictOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image');
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('gallery_category_id');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};