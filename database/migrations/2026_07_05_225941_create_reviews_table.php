<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('comment');
            $table->date('review_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('rating');
            $table->index('is_published');
            $table->index('review_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};