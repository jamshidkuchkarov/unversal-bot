<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('title')->nullable();
            $table->text('message_text');
            $table->string('media_path')->nullable();
            $table->json('media_files')->nullable();
            $table->enum('media_type', ['none', 'photo', 'video', 'document', 'animation'])->default('none');
            $table->json('inline_buttons')->nullable();
            $table->enum('target_type', ['all_users', 'channel', 'group', 'specific_users'])->default('all_users');
            $table->string('target_channel')->nullable();
            $table->json('target_user_ids')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'failed', 'cancelled'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_schedule')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('scheduled_at');
        });

        Schema::create('announcement_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->bigInteger('telegram_user_id');
            $table->enum('status', ['pending', 'sent', 'failed', 'blocked'])->default('pending');
            $table->integer('telegram_message_id')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['announcement_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_recipients');
        Schema::dropIfExists('announcements');
    }
};
