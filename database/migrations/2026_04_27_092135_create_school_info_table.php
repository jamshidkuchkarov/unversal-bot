<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained('schools')->cascadeOnDelete();

            // About section (multilingual)
            $table->longText('about_text_uz')->nullable();
            $table->longText('about_text_ru')->nullable();

            // History section (multilingual)
            $table->longText('history_text_uz')->nullable();
            $table->longText('history_text_ru')->nullable();

            // Mission & Vision (multilingual)
            $table->text('mission_text_uz')->nullable();
            $table->text('mission_text_ru')->nullable();
            $table->text('vision_text_uz')->nullable();
            $table->text('vision_text_ru')->nullable();

            // Director info
            $table->string('director_name')->nullable();
            $table->string('director_photo')->nullable();
            $table->text('director_message_uz')->nullable();
            $table->text('director_message_ru')->nullable();

            // Achievements (JSON array)
            $table->json('achievements')->nullable();

            // Gallery (JSON array of image paths)
            $table->json('gallery_images')->nullable();

            // Video
            $table->string('video_url')->nullable();

            // Contact info
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();

            // Address (multilingual)
            $table->text('address_uz')->nullable();
            $table->text('address_ru')->nullable();

            // Map coordinates
            $table->decimal('map_latitude', 10, 8)->nullable();
            $table->decimal('map_longitude', 11, 8)->nullable();

            // Working hours (JSON)
            $table->json('working_hours')->nullable();

            // Social links (JSON)
            $table->json('social_links')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_info');
    }
};
