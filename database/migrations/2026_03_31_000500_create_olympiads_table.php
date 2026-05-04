<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('olympiads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title');
            $table->string('subject')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('target_classes')->nullable();
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->dateTime('registration_start');
            $table->dateTime('registration_end');
            $table->date('olympiad_date')->nullable();
            $table->string('olympiad_location')->nullable();
            $table->integer('max_participants')->nullable();
            $table->boolean('is_free')->default(true);
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'closed', 'completed', 'cancelled'])->default('draft');
            $table->text('result_text')->nullable();
            $table->boolean('results_published')->default(false);
            $table->string('announcement_message_id')->nullable();
            $table->boolean('announced_to_channel')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('registration_end');
        });

        Schema::create('olympiad_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('olympiad_id')->constrained('olympiads')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('bot_session_id')->nullable()->constrained('bot_sessions')->nullOnDelete();
            $table->bigInteger('telegram_user_id')->nullable();
            $table->string('full_name');
            $table->integer('class_number')->nullable();
            $table->string('class_letter')->nullable();
            $table->string('phone');
            $table->string('district')->nullable();
            $table->string('school_name_custom')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'free'])->default('free');
            $table->string('payment_ref')->nullable();
            $table->integer('score')->nullable();
            $table->integer('place')->nullable();
            $table->string('prize')->nullable();
            $table->boolean('result_sent')->default(false);
            $table->enum('status', ['registered', 'confirmed', 'cancelled', 'participated', 'absent'])->default('registered');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['olympiad_id', 'telegram_user_id']);
            $table->index(['olympiad_id', 'status']);
            $table->index('school_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('olympiad_registrations');
        Schema::dropIfExists('olympiads');
    }
};
